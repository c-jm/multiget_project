<?php

require_once("./vendor/autoload.php");
require_once("./util.php");
require_once("./MultiGetChunk.php");

use \GuzzleHttp\Client       as GuzzleClient;
use \GuzzleHttp\Psr7\Request as GuzzleRequest;

// Class Name: MultiGetClient
//
// A MultiGetClient used to download a resource from a particular URI in chunks.
// The main purpose for this is to stream content and make downloads easier for network load.
//
//

class MultiGetClient
{
    private $url;           // The URL we are downloading from.
    private $guzzleClient;  // The client we are using.

    // Callbacks
    private $perChunkCallback; // A callback we want to call for every segment.
    private $finishedCallback; // A callback for when we are finished.

    private $segmentSize = 0; // The segment size in MB.
    private $chunkSize = 0; // The chunk size in MB.

    private $currentChunks; // The current segments we are processing.
    private $currentProcessedBytes; // The co unt of the current bytes we have processed.


    // Specials

    public function __construct($url, $segmentSize, $chunkSize)
    {
        $this->url = $url;
        $this->guzzleClient = new GuzzleClient();
        $this->segmentSize = $segmentSize;
        $this->chunkSize = $chunkSize;
    }

    // Setters

    public function setPerChunkCallback($cb)
    {
        $this->perChunkCallback = $cb;
    }

    public function setFinishedCallback($cb)
    {
        $this->finishedCallback = $cb;
    }


    // Method Name: buildStats()
    //
    // $chunk: The chunk we are currently processing, mainly used to store on the end of the currentChunks.
    //
    // Advances the current stream of the MultiGet by the number of bytes within
    // a defined chunk. Monitors the currently processed chunks through the $currentChunks array.
    //
    //
    private function processChunk($chunk)
    {
        $this->currentProcessedBytes += $this->chunkSize;
        $this->currentChunks[] = $chunk;
    }

    // Method Name: resetState()
    //
    // Resets the state of the client to perform another MultiGet request.
    private function resetState()
    {
        $this->currentChunks = [];
        $this->currentProcessedBytes = 0;
    }


    // Method Name: buildStats()
    //
    // Returns a well formed array of current stats on this MultiGet request.
    //
    // Returns:
    //
    // An array containing stats of this multi get request.
    private function buildStats()
    {
        return ['bytes_processed' => $this->currentProcessedBytes,
                'number_of_get_requests' => count($this->currentChunks),
                'chunks' => $this->currentChunks];
    }

    // Method Name: sendRangeRequest($chunk)
    //
    // $chunk: The chunk we are sending.
    //
    // Takes a defined chunk and sends it as a range header for the resource.
    //
    // Returns:
    //
    // The response from Guzzle for this chunk

    private function sendRangeRequest($chunk)
    {
        // A chunk then knows how to convert itself into its range representation which is 'bytes=START-END'.
        // This uses the __toString functionality.
        $rangeHeaderString = (string)$chunk;

        // We use Guzzle to  send the actual request.
        $req = new GuzzleRequest('GET', $this->url, ['Range' => $rangeHeaderString]);

        //
        return $this->guzzleClient->send($req);
    }

    public function fetch()
    {
        // Reset state every time we fetch.
        $this->resetState();

        // This is the main loop.
        while ($this->currentProcessedBytes < $this->segmentSize) {
            // We create a chunk from where we are currently + the size of a chunk.
            $chunk = new MultiGetChunk($this->currentProcessedBytes, ($this->currentProcessedBytes + $this->chunkSize));
            
            // We send a range request with the current chunk.
            $response = $this->sendRangeRequest($chunk);

            // Process the current chunk updating the number of bytes we have processed and adding the chunk to currentChunks.
            $this->processChunk($chunk);

            callCallback($this->perChunkCallback, $response);
        }

        // We then setup some stats and call a finish callback whiich allows the user to do what they want when finished.
        // In our case we are just printing hte stats that were passed in.
        callCallback($this->finishedCallback, $this->buildStats());
    }
}

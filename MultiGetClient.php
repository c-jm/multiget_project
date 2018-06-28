<?php

require_once("./vendor/autoload.php");
require_once("./util.php");
require_once("./MultiGetChunk.php");

use \GuzzleHttp\Client       as GuzzleClient;
use \GuzzleHttp\Psr7\Request as GuzzleRequest;

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
    //
    public function __construct($url, $segmentSize, $chunkSize)
    {
        $this->url = $url;
        $this->guzzleClient = new GuzzleClient();
        $this->segmentSize = $segmentSize;
        $this->chunkSize = $chunkSize;
    }

    // Setters
    //

    public function setPerChunkCallback($cb)
    {
        $this->perChunkCallback = $cb;
    }

    public function setFinishedCallback($cb)
    {
        $this->finishedCallback = $cb;
    }

    public function fetch()
    {
        // Reset state every time we fetch.
        $this->resetState();

        // This is the main loop.
        while ($this->currentProcessedBytes < $this->segmentSize) {
            // We create a segment from where we are currently + the size of a chunk.
            $chunk = new MultiGetChunk($this->currentProcessedBytes, ($this->currentProcessedBytes + $this->chunkSize));
            
            // We send a range request with the current segment.
            $response = $this->sendRangeRequest($chunk);

            // Process the current chunk updating the number of bytes we have processed and adding the chunk to currentChunks.  
            $this->processChunk($chunk);

            callCallback($this->perChunkCallback, $response);
        }

        // We then setup some stats and call a finish callback whiich allows the user to do what they want when finished. 
        // In our case we are just printing hte stats that were passed in.
        callCallback($this->finishedCallback, $this->buildStats());
    }

    private function processChunk($chunk)
    {
        $this->currentProcessedBytes += $this->chunkSize;
        $this->currentChunks[] = $chunk;
    }

    private function resetState()
    {
        $this->currentChunks = [];
        $this->currentProcessedBytes = 0;
    }

    private function buildStats()
    {
        return ['bytes_processed' => $this->currentProcessedBytes, 'number_of_get_requests' => count($this->currentChunks), 'chunks' => $this->currentChunks];
    }

    private function sendRangeRequest($chunk)
    {
        // A chunk then knows how to convert itself into its range representation which is 'bytes=START-END'.
        // This uses the __toString functionality.
        $rangeHeaderString = (string)$chunk;

        // We use Guzzle to  send the actual request.
        $req = new GuzzleRequest('GET', $this->url, ['Range' => $rangeHeaderString]);

        return $this->guzzleClient->send($req);
    }
}

<?php

require_once("./vendor/autoload.php");

use \GuzzleHttp\Client       as GuzzleClient;
use \GuzzleHttp\Psr7\Request as GuzzleRequest;
use \Measurements\Bytes\MegaBytes as MB;

class MultiGetClient
{
    private $url;           // The URL we are downloading from.
    private $guzzleClient;  // The client we are using.
    private $perSegmentCallback; // A callback we want to call for every segment.

    // Specials
    //
    public function __construct($url)
    {
        $this->url = $url;
        $this->guzzleClient = new GuzzleClient();
    }

    public function setPerSegmentCallback($cb) {
        $this->perSegmentCallback = $cb;
    }

    public function fetch()
    {
        $segmentSize = MB::allocateUnits(4)->numberOfBytes();
        $chunkSize = MB::allocateUnits(1)->numberOfBytes();
        $currentlyProcessed = 0;

        while ($currentlyProcessed < $segmentSize) {
            $segmentStart = $currentlyProcessed;
            $segmentEnd   = ($currentlyProcessed + $chunkSize);

            $rangeHeaderString = $this->buildRangeString($segmentStart, $segmentEnd);
            $req = new GuzzleRequest('GET', $this->url, ['Range' => $rangeHeaderString]);
            $response = $this->guzzleClient->send($req);

            $currentlyProcessed += $chunkSize;

            $perSegCallback = $this->perSegmentCallback;
            $perSegCallback($response);
        }
    }

    private function buildRangeString($start, $end)
    {
        return sprintf("bytes=%d-%d", $start, $end);
    }
}

function dd($value)
{
    var_dump($value);
    die();
}

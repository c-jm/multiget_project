<?php

require_once("./vendor/autoload.php");

use \GuzzleHttp\Client       as GuzzleClient;
use \GuzzleHttp\Psr7\Request as GuzzleRequest;
use \Measurements\Bytes\MegaBytes as MB;

class MultiGetClient
{
    private $url;           // The URL we are downloading from.
    private $guzzleClient;  // The client we are using.

    // Specials
    //
    public function __construct($url)
    {
        $this->url = $url;
        $this->guzzleClient = new GuzzleClient();
    }

    public function fetch()
    {
        $chunkSegmentSize = MB::allocateUnits(1)->numberOfBytes();

        $rangeHeaderString = $this->buildRangeString(0, $chunkSegmentSize);

        $req = new GuzzleRequest('GET', $this->url, ['Range' => $rangeHeaderString]);
        $response = $this->guzzleClient->send($req);

        dd($response);
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

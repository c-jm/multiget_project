<?php

require_once("./vendor/autoload.php");

use \GuzzleHttp\Client       as GuzzleClient;
use \GuzzleHttp\Psr7\Request as GuzzleRequest;
use \Measurements\Bytes\MegaBytes as MB;

class MultiGetClient
{
    private $url;           // The URL we are downloading from.
    private $guzzleClient;  // The client we are using.
    private $currentRequest; // The current request we are processing.

    // Specials
    //
    public function __construct($url)
    {
        $this->url = $url;
        $this->guzzleClient = new GuzzleClient();
    }

    public function fetch()
    {
    }
}

<?php

require_once("./vendor/autoload.php");

use \GuzzleHttp\Client as GuzzleClient;

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
        printf("From fetch\n");
    }
}


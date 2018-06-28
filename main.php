<?php

require_once("./MultiGetClient.php");

const BASE_URL = 'http://785e6149.bwtest-aws.pravala.com/384MB.jar';

function main()
{
    $client = new MultiGetClient(BASE_URL);

    $client->setPerSegmentCallback(function($response) {
        $content = $response->getBody();
        file_put_contents("test.jar", $content, FILE_APPEND);
    });


    $client->fetch();

    return 0;
}

main();

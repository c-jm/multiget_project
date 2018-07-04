<?php

require_once("./ArgParser.php");
require_once("./MultiGetClient.php");

use \Measurements\Bytes\MegaBytes as MB;

const BASE_URL = 'http://785e6149.bwtest-aws.pravala.com/384MB.jar';

function main()
{
    $options = ArgParser::validateArgs();

    //(NOTE: cjm): This is an assumption because I am assuming the user always enter as int.
    $chunkSize = MB::allocateUnits((int)$options['chunkSize'])->numberOfBytes();
    $segmentSize = MB::allocateUnits((int)$options['segmentSize'])->numberOfBytes();

    // Create a new client and set call backs appropriately.
    $client = new MultiGetClient($options['url'], $segmentSize, $chunkSize);

    printf("Downloading first %d chunks from url %s to file: %s", $options['segmentSize'], $options['url'], $options['outputFilename']);
    printf("\n");

    $client->setPerChunkCallback(function ($response) use ($options) {
        printf('.');
        $content = $response->getBody();
        file_put_contents($options['outputFilename'], $content, FILE_APPEND);
    });

    $client->setFinishedCallback(function ($stats) {
        printf("done\n");
    });

    // Finally fetch the resource by and watch it happen.
    $client->fetch();

    return 0;
}

main();

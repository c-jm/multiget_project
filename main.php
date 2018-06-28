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

    $client->setPerChunkCallback(function ($response) use ($options) {
        $content = $response->getBody();
        file_put_contents($options['outputFilename'], $content, FILE_APPEND);
    });

    $client->setFinishedCallback(function ($stats) {
        printf("=========\n");
        printf("Bytes Processed: %d\n # of Get Requests: %d\n", $stats['bytes_processed'], $stats['number_of_get_requests']);
        printf("=========\n");

        foreach ($stats['chunks'] as $chunkIndex => $chunk) {
            printf(" Chunk: %d\n Start: %d\n End: %d\n", ($chunkIndex + 1), $chunk->start, $chunk->end);
            printf("=========\n");
        }
    });

    // Finally fetch the resource by and watch it happen.
    $client->fetch();

    return 0;
}

main();

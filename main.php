<?php

require_once("./MultiGetClient.php");

use \Measurements\Bytes\MegaBytes as MB;

const BASE_URL = 'http://785e6149.bwtest-aws.pravala.com/384MB.jar';


class ArgParser
{
    const ARGS="o::u:c::s::";

    public static function validateArgs()
    {
        $options = getopt(ArgParser::ARGS);

        if (count($options) < 1) {
            usage();
        }

        foreach ($options as $v) {
            if (! $v) {
                usage();
            }
        }


        return ['url' => $options['u'],
            'outputFilename' => isset($options['o']) ? $options['o'] : 'output.file',
            'segmentSize' => isset($options['s']) ? $options['s'] : 4,
            'chunkSize' => isset($options['c']) ? $options['c'] : 1];
    }
}
function main()
{
    $options = ArgParser::validateArgs();

    $chunkSize = MB::allocateUnits((int)$options['chunkSize'])->numberOfBytes();
    $segmentSize = MB::allocateUnits((int)$options['segmentSize'])->numberOfBytes();

    $client = new MultiGetClient($options['url'], $segmentSize, $chunkSize);

    $client->setPerChunkCallback(function ($response) use ($options) {
        $content = $response->getBody();
        file_put_contents($options['outputFilename'], $content, FILE_APPEND);
    });

    $client->setFinishedCallback(function ($stats) {
        var_dump($stats);
    });

    $client->fetch();

    return 0;
}

main();

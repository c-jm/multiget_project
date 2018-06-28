<?php


// Class Name: ArgParser
//
// An ArgParser is used to validate and setup all the correct defaults arguments
// for the initial program.

class ArgParser
{
    const ARGS="o::u:c::s::";

    public static function validateArgs()
    {
        // Get the correct arguments.
        $options = getopt(ArgParser::ARGS);

        // Throw usage if we dont have enough.
        if (count($options) < 1) {
            usage();
        }

        // If there are any which are requried and blank then throw usage.
        foreach ($options as $v) {
            if (! $v) {
                usage();
            }
        }


        // Finally return a well formed array with good option names.
        return ['url' => $options['u'],
                'outputFilename' => isset($options['o']) ? $options['o'] : 'output.file',
                'segmentSize' => isset($options['s']) ? $options['s'] : 4,
                'chunkSize' => isset($options['c']) ? $options['c'] : 1];
    }
}

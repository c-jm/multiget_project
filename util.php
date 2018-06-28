<?php

// callCallback($name, $params)
//
// A simple utility that helps make closures easier to call.
//
// $name: The function we are calling.
// $params: The parameters.
//
// NOTE: I wrote this because I dont like how youo have to pass a string to call_user_func.
function callCallback($name, $params)
{
    $fn = $name;
    $fn($params);
}

// dd($value)
//
// A simple clone f Laravel and Symphony's dd function.
//
function dd($value)
{
    var_dump($value);
    die();
}

function usage()
{
    printf("Usage: php main.php [-[u]rl -[o]utput-file -[c]hunk-size -[s]egment-size\n");
    exit();
}

<?php

function callCallback($name, $params)
{
    $fn = $name;
    $fn($params);
}
function dd($value)
{
    var_dump($value);
    die();
}

function usage()
{
    printf("Usage: php main.php [-[u]rl -[o]utput-file\n");
    exit();
}

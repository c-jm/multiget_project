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

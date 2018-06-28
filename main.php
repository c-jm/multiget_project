<?php

require_once("./MultiGetClient.php");

const BASE_URL = 'http://785e6149.bwtest-aws.pravala.com/384MB.jar';

function main()
{
    $client = new MultiGetClient(BASE_URL);
    $client->fetch();

    return 0;
}

main();

<?php

function json_response($data)
{
    header('Content-Type: application/json');
    return json_encode($data);
}

function dd($data)
{
    var_dump($data);die;
}
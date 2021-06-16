<?php

class TestController 
{
    public function index(Request $request)
    {
        return json_response([
            'tata' => "data"
        ]);
    }

    public function detail(Request $request)
    {
        return json_response([
            "data" => 'DATATATATA'
        ]);
    }

    public function test(Request $request)
    {
        return json_response([
            'tata' => "data"
        ]);
    }
}
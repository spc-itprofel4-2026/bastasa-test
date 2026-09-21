<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function hello($name = null)
    {
        $data = [
            'name' => $name ?? 'World'
        ];
        return view('hello', $data);
    }
}

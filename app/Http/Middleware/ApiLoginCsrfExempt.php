<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class ApiLoginCsrfExempt extends Middleware
{
    protected $except = [
        'api-login',
        '/api-login',
    ];
}

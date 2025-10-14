<?php

return [
    'secret' => env('JWT_TOKEN_SECRET'),
    'expired_token' => env('JWT_EXPIRED_TOKEN',8), //DEFAUL 8 HORAS
    'receivers' => [
        'http://192.168.20.116:5173'
    ]
];
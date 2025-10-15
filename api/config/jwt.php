<?php

return [
    'secret' => env('JWT_TOKEN_SECRET'),
    'expired_token' => env('JWT_EXPIRED_TOKEN',8), //DEFAUL 8 HORAS
    'receivers' => [
        env('JWT_RECEIVER_PRINCIPAL','http://localhost')
    ]
];
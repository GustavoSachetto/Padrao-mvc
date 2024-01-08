<?php

use App\Http\Response;

// ROTA ADMIN
$obRouter->get('/admin', [
    'middlewares' => [
        'required-admin-login'
    ],
    function(){
        return new Response(200, 'admin');
    }
]);
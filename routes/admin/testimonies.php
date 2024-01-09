<?php

use App\Http\Response;
use App\Controller\Admin;

// ROTA ADMIN DE DEPOIMENTOS
$obRouter->get('/admin/testimonies', [
    'middlewares' => [
        'required-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Testimony::getTestimonies($request));
    }
]);
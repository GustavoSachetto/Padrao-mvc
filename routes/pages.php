<?php

use App\Http\Response;
use App\Controller\Pages;

// ROTA HOME
$obRouter->get('/', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function(){
        return new Response(200, Pages\Home::getHome());
    }
]);

// ROTA SOBRE
$obRouter->get('/about', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function(){
        return new Response(200, Pages\About::getAbout());
    }
]);

// ROTA DEPOIMENTOS
$obRouter->get('/testimonies', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function($request){
        return new Response(200, Pages\Testimony::getTestimonies($request));
    }
]);

// ROTA DEPOIMENTOS (INSERT)
$obRouter->post('/testimonies', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function($request){
        return new Response(200, Pages\Testimony::insertTestimony($request));
    }
]);
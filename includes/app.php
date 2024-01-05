<?php

require __DIR__.'/../vendor/autoload.php';

use App\Common\Environment;
use App\Utils\View;
use App\Model\DatabaseManager\Database;

// CARREGA AS VARIAVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

// DEFINE AS CONFIGURAÇÕES DE BANDO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'), 
    getenv('DB_PORT')
);

// DEFINE CONSTANTE DE URL
define('URL', getenv('URL'));

// DEFINE O VALOR PADRÃO DAS VARIAVEIS
View::init([
    'URL' => URL
]);
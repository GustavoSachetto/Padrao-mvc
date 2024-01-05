<?php 

require __DIR__.'/vendor/autoload.php';

use App\Utils\View;
use App\Http\Router;

define('URL', 'http://localhost/Sistema-login');

View::init([
    'URL' => URL
]);

$obRouter = new Router(URL);

// INCLUI AS ROTAS DE PÁGINAS
include __DIR__.'/routes/pages.php';

// IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendReponse();
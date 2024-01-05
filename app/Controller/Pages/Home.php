<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class Home extends Page
{
    /**
     * Método responsável por retornar o conteúdo (view) da nossa home
     * @return string
     */
    public static function getHome() {
        // ORGANIZAÇÃO
        $obOrganization = new Organization();

        // VIEW DA HOME
        $title = 'Página home';
        $content = View::render('pages/home', [
            'name' => $obOrganization->name
        ]);
        // RETORNA A VIEW DA PÁGINA
        return parent::getPage($title, $content);
    }
}

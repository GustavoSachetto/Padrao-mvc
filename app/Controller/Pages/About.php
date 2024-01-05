<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class About extends Page
{
    /**
     * Método responsável por retornar o conteúdo (view) da nossa about
     * @return string
     */
    public static function getAbout() {
        // ORGANIZAÇÃO
        $obOrganization = new Organization();

        // VIEW DA PAGINA ABOUT
        $title = 'Página about';
        $content = View::render('pages/about', [
            'name' => $obOrganization->name,
            'description' => $obOrganization->description
        ]);
        // RETORNA A VIEW DA PÁGINA
        return parent::getPage($title, $content);
    }
}

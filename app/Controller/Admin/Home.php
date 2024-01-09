<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Home extends Page
{
    /**
     * Método responsavel por renderizar a view de home no paínel
     * @param Request
     * @return string
     */
    public static function getHome()
    {
        // CONTEÚDO DA HOME
        $content = View::render('admin/modules/home/index', []);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Página home', $content, 'home');
    }
}

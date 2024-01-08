<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert
{
    /**
     * Método responsável por retornar uma mensagem de sucesso
     * @param string $mensagem
     * @return string
     */
    public static function getSuccess($mensagem)
    {
        return View::render('admin/alert/status', [
            'tipo'=> 'success',
            'mensagem'=> $mensagem
        ]);
    }

    /**
     * Método responsável por retornar uma mensagem de erro
     * @param string $mensagem
     * @return string
     */
    public static function getError($mensagem)
    {
        return View::render('admin/alert/status', [
            'tipo'=> 'danger',
            'mensagem'=> $mensagem
        ]);
    }
}

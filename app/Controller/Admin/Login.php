<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\User;
use App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page 
{
    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        // STATUS
        $status = !is_null($errorMessage) ? View::render('admin/login/status', [
            'mensagem' => $errorMessage
        ]) : '';

        // CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('admin/login', [
            'status' => $status
        ]);
        
        // RETORNA A PÁGINA COMPLETA
        return parent::getPage('Página login', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     * @return void
     */
    public static function setLogin($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();
        $email    = $postVars['email'] ?? '';
        $senha    = $postVars['senha'] ?? '';

        // BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($email);
        if (!$obUser instanceof User) {
            return self::getLogin($request, 'E-mail ou Senha inválidos!');
        }

        // VERIFICA A SENHA DO USUÁRIO
        if (!password_verify($senha, $obUser->senha)) {
           return self::getLogin($request, 'E-mail ou Senha inválidos!');
        }

        // CRIA A SESSÃO DE LOGIN
        SessionAdminLogin::login($obUser);

        // REDIRECIONA O USUÁRIO PARA A HOME DO ADMIN
        $request->getRouter()->redirect('/admin');
    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     * @return void
     */
    public static function setLogout($request)
    {
        // DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::logout();

        // REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/admin/login');
    }
}

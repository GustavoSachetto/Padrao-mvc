<?php

namespace App\Http\Middleware;

use Exception;
use Firebase\JWT\JWT;
use App\Model\Entity\User;

class JWTAuth
{
    /**
     * Método responsável por retornar uma instância de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request)
    {
        // HEADERS
        $headers = $request->getHeaders();
        
        // TOKEN PURO EM JWT
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer', '', $headers['Authorization']) : '';

        // BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        // VERIFICA A INSTÂNCIA 
        if (!$obUser instanceof User) {
            return false;
        }

        // VALIDA A SENHA E RETORNA O USUÁRIO
        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }

    /**
     * Método responsável por validar o acesso via JWT
     * @param Request $request
     * @return void
     */
    private function auth($request)
    {
        // VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        // EMITE O ERRO DE SENHA INVÁLIDA
        throw new Exception("Acesso negado", 403);
    }

    /**
     * Método reponsável por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Reponse
     */
    public function handle($request, $next)
    {
        // REALIZA A VALIDAÇÃO DO ACESSO VIA JWT
        $this->auth($request);

        // EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

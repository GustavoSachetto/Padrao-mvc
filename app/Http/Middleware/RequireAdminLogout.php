<?php

namespace App\Http\Middleware;

use App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogout
{
    /**
     * Método reponsável por executar o middleware
     * @param Request $request
     * @param Closure $next
     * @return Reponse
     */
    public function handle($request, $next)
    {
        // VERIFICA SE O USUÁRIO ESTÁ LOGADO
        if (SessionAdminLogin::isLogged()) {
            $request->getRouter()->redirect('/admin');
        }      

        return $next($request);
    }
}

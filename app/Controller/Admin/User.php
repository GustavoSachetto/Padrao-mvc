<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\DatabaseManager\Pagination;
use App\Model\Entity\User as EntityUser;
use App\Session\Admin\Login as SessionAdminLogin;

class User extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de usuários da página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItens($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quatidadetotal = EntityUser::getUsers(null, null, null,'COUNT(*) as qtn')->fetchObject()->qtn;

        // PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quatidadetotal, $paginaAtual, 5);

        // RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null,'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)) {
            $itens .= View::render('admin/modules/users/itens', [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email,
                'senha' => $obUser->senha
            ]);
        }

        // RETORNA OS USUÁRIOS
        return $itens;
    }

    /**
     * Método responsavel por renderizar a view de usuários no paínel
     * @param Request
     * @return string
     */
    public static function getUsers($request)
    {
        // CONTEÚDO DA HOME
        $content = View::render('admin/modules/users/index', [
            'itens'      => self  ::getUserItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'   => self::getStatus($request)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Página usuários', $content, 'users');
    }

    /**
     * Métoto reponsável por retornar o formulário de cadastro de um novo usuário
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        // CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'    => 'Cadastrar usuário',
            'nome'     => '',
            'email' => '',
            'labelSenha' => 'Senha',
            'requisicao' => 'required',
            'status'   => self::getStatus($request)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar usuário', $content, 'users');
    }

    /**
     * Métoto reponsável por cadastrar um novo usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        // POST VARS
        $postvars = $request->getPostVars();
        $nome  = $postvars['nome'] ?? '';
        $email = $postvars['email'] ?? '';
        $senha = $postvars['senha'] ?? '';

        // VÁLIDA INSTÂNCIA DE USUÁRIO
        $obEntityUser = EntityUser::getUserByEmail($email);

        if ($obEntityUser instanceof EntityUser) {
            // REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users?status=duplicated');
        }

        // NOVA INSTÂNCIA DE USUÁRIO
        $obUser = new EntityUser;
        $obUser->nome  = $nome ?? '';
        $obUser->email = $email ?? '';
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT) ?? '';
        $obUser->cadastrar();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    public static function getStatus($request)
    {
        // QUERY PARAMS
        $queryParams = $request->getQueryParams();

        // STATUS
        if (!isset($queryParams['status']))  return '';

        // MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Usuario criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuario atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuario excluído com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('Usuario não criado, email já existente.');
                break;
            case 'integrated':
                return Alert::getError('Você não pode alterar um usuário que esteja logado.');
                break;
        }
    }

    /**
     * Métoto reponsável por retornar o formulário de edição de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id)
    {
        // PEGANDO A INSTÂNCIA DE LOGIN
        $UserLogin = SessionAdminLogin::getLogged();        
        
        // VERIFICANDO O ID DO USUÁRIO LOGADO
        if ($UserLogin['id'] == $id) {
            // REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users?status=integrated');
        } 

        // NOVA INSTÂNCIA DE USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTÃNCIA
        if (!$obUser instanceof EntityUser) {
            // REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users');
        } 

        // POST VARS
        $postVars = $request->getPostVars();
        $nome  = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // CONTEÚDO DO FORMULÁRIO DE EDIÇÃO
        $content = View::render('admin/modules/users/form', [
            'title'  => 'Editar usuário',
            'nome'   => $obUser->nome,
            'email'  => $obUser->email,
            'labelSenha'  => 'Nova senha',
            'requisicao' => '',
            'status' => self::getStatus($request)
        ]);

        
        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar usuário', $content, 'users');
    }

    /**
     * Método reponsável por gravar a atualização de um usuário
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public static function setEditUser($request, $id)
    {
        // NOVA INSTÂNCIA DE USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTÂNCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }
        
        // POST VARS
        $postVars = $request->getPostVars();
        $nome  = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // INSTÂNCIA DE USUÁRIO
        $obUser->nome  = $nome ?? $obUser->nome;
        $obUser->email = $email ?? $obUser->email;
        $obUser->senha = $senha != null ? password_hash($senha, PASSWORD_DEFAULT) : $obUser->senha;

        $obUser->atualizar();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }

    /**
     * Métoto reponsável por retornar o formulário de exclusão de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id)
    {
        // PEGANDO A INSTÂNCIA DE LOGIN
        $UserLogin = SessionAdminLogin::getLogged();        

        // VERIFICANDO O ID DO USUÁRIO LOGADO
        if ($UserLogin['id'] == $id) {
            // REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users?status=integrated');
        } 

        // NOVA INSTÂNCIA DE USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // CONTEÚDO DO FORMULÁRIO DE EDIÇÃO
        $content = View::render('admin/modules/users/delete', [
            'email' => $obUser->email
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Deletar usuário', $content, 'users');
    }
    /**
     * Método reponsável por deletar um usuário existente
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public static function setDeleteUser($request, $id)
    {
        // NOVA INSTÂNCIA DE USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        $obUser->excluir();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}

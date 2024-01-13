<?php

namespace App\Controller\Api;

use Exception;
use App\Model\DatabaseManager\Pagination;
use App\Model\Entity\User as EntityUser;

class User extends Api
{
     /**
     * Método responsável por obter a renderização dos itens de usuários da api
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItens($request, &$obPagination)
    {
        // USUÁRIOS
        $itens = [];

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
            $itens[] = [
                'id'     => $obUser->id,
                'nome'   => $obUser->nome,
                'email'  => $obUser->email
            ];
        }

        // RETORNA OS USUÁRIOS
        return $itens;
    }

    /**
     * Método responsável por retornar os usuários cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers($request)
    {
        return [
            'usuarios'  => self  ::getUserItens($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar um usuário individual
     * @param Request $request
     * @return array
     */
    public static function getUser($request, $id)
    {
        // VALIDA O ID DO USUÁRIO
        if (!is_numeric($id)) {
            throw new Exception("O id ".$id." não é válido.", 400);
        }
        // BUSCA USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VÁLIDA SE O USUÁRIO EXISTE
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }

        // RETORNA OS DETALHES DO USUÁRIO   
        return [
            'id'     => $obUser->id,
            'nome'   => $obUser->nome,
            'email'  => $obUser->email
        ];
    }

    /**
     * Método responsável por cadastrar um novo usuário
     * @param Request $request
     * @return array
     */
    public static function setNewUser($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();
        $nome  = $postVars['nome'];
        $email = $postVars['email'];
        $senha = $postVars['senha'];

        // VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($nome) || !isset($email) || !isset($senha)) {
            throw new Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios.", 400);
        }
        
        // VALIDA INSTANCIA DE USUÁRIO
        $obEntityUser = EntityUser::getUserByEmail($email);
        
        if ($obEntityUser instanceof EntityUser) {
            throw new Exception("Usuario não criado, email já existente.", 400);
        }

        // NOVA INSTANCIA DE USUÁRIO
        $obUser = new EntityUser;

        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->cadastrar();
        
        // RETORNA OS DETALHES DO USUÁRIO CADASTRADO
        return [
            'id'     => $obUser->id,
            'nome'   => $obUser->nome,
            'email'  => $obUser->email,
            'status'   => 'success'
        ];
    }

    /**
     * Método responsável por atualizar um usuário
     * @param Request $request
     * @return array
     */
    public static function setEditUser($request, $id)
    {   
        // POST VARS
        $postVars = $request->getPostVars();

        // VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])) {
            throw new Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios.", 400);
        }

        // NOVA INSTANCIA DE USUÁRIO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }

        // ATUALIZA O USUÁRIO
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->atualizar();
        
        // RETORNA OS DETALHES DO USUÁRIO ATUALIZADO
        return [
            'id'     => $obUser->id,
            'nome'   => $obUser->nome,
            'email'  => $obUser->email,
            'status'   => 'success'
        ];
    }

    /**
     * Método responsável por deletar um depoimento
     * @param Request $request
     * @return array
     */
    public static function setDeleteUser($request, $id)
    {
        // NOVA INSTANCIA DE DEPOIMENTO
        $obUser = EntityUser::getUserById($id);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário ".$id." não foi encontrado.", 404);
        }

        if ($obUser->id == $request->user->id) {
            throw new Exception("Não é possivel excluir o cadastro atualmente conectado", 400);
        }

        // DELETA O DEPOIMENTO
        $obUser->excluir();
        
        // RETORNA O STATUS DO DEPOIMENTO DELETADO
        return [
            'status'   => 'success'
        ];
    }
}

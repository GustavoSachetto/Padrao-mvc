<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\DatabaseManager\Pagination;
use App\Model\Entity\Testimony as EntityTestimony;

class Testimony extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos da página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItens($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quatidadetotal = EntityTestimony::getTestimonies(null, null, null,'COUNT(*) as qtn')->fetchObject()->qtn;

        // PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quatidadetotal, $paginaAtual, 5);

        // RESULTADOS DA PÁGINA
        $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            $itens .= View::render('admin/modules/testimonies/itens', [
                'id' => $obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsavel por renderizar a view de depoimentos no paínel
     * @param Request
     * @return string
     */
    public static function getTestimonies($request)
    {
        // CONTEÚDO DA HOME
        $content = View::render('admin/modules/testimonies/index', [
            'itens'      => self  ::getTestimonyItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'   => self::getStatus($request)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Página depoimentos', $content, 'testimonies');
    }

    /**
     * Métoto reponsável por retornar o formulário de cadastro de um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewTestimony($request)
    {
        // CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/form', [
            'title'    => 'Cadastrar depoimento',
            'nome'     => '',
            'mensagem' => '',
            'status'   => self::getStatus($request)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar depoimento', $content, 'testimonies');
    }

    /**
     * Métoto reponsável por cadastrar um novo depoimento no banco
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request)
    {
        // POST VARS
        $postvars = $request->getPostVars();

        // NOVA INSTÂNCIA DE DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome     = $postvars['nome'] ?? '';
        $obTestimony->mensagem = $postvars['mensagem'] ?? '';
        $obTestimony->cadastrar();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
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
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento excluído com sucesso!');
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * Métoto reponsável por retornar o formulário de edição de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request, $id)
    {
        // NOVA INSTÂNCIA DE DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // CONTEÚDO DO FORMULÁRIO DE EDIÇÃO
        $content = View::render('admin/modules/testimonies/form', [
            'title'    => 'Editar depoimento',
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status'   => self::getStatus($request)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Editar depoimento', $content, 'testimonies');
    }

    /**
     * Método reponsável por gravar a atualização de um depoimento
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public static function setEditTestimony($request, $id)
    {
        // NOVA INSTÂNCIA DE DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }
        
        // POST VARS
        $postvars = $request->getPostVars();

        // INSTÂNCIA DE DEPOIMENTO
        $obTestimony->nome     = $postvars['nome'] ??  $obTestimony->nome;
        $obTestimony->mensagem = $postvars['mensagem'] ??  $obTestimony->mensagem;

        $obTestimony->atualizar();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
    }

    /**
     * Métoto reponsável por retornar o formulário de edição de um depoimento
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request, $id)
    {
        // NOVA INSTÂNCIA DE DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        // CONTEÚDO DO FORMULÁRIO DE EDIÇÃO
        $content = View::render('admin/modules/testimonies/delete', [
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Deletar depoimento', $content, 'testimonies');
    }
    /**
     * Método reponsável por deletar um depoimento existente
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public static function setDeleteTestimony($request, $id)
    {
        // NOVA INSTÂNCIA DE DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        $obTestimony->excluir();

        // REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }
}

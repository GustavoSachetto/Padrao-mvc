<?php

namespace App\Controller\Pages;

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
        // DEPOIMENOTS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quatidadetotal = EntityTestimony::getTestimonies(null, null, null,'COUNT(*) as qtn')->fetchObject()->qtn;

        // PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quatidadetotal, $paginaAtual, 1);

        // RESULTADOS DA PÁGINA
        $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            $itens .= View::render('pages/testimony/item', [
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }
    /**
     * Método responsável por retornar o conteúdo (view) de depoimentos
     * @return string
     */
    public static function getTestimonies($request) {
        // VIEW DE DEPOIMENTOS
        $title = 'Página depoimentos';
        $content = View::render('pages/testimonies', [
            'itens' => self::getTestimonyItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ]);
        // RETORNA A VIEW DA PÁGINA
        return parent::getPage($title, $content);
    }

    /**
     * Método responsável por cadastrar um depoimento
     * @param Request $request
     * @return string
     */
    public static function insertTestimony($request)
    {
        // DADOS DO POST
        $postVars = $request->getPostVars();

        // NOVA INSTANCIA DE DEPOIMENTO
        $obTestimony = new EntityTestimony;

        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();
        
        // RETORNA A PÁGINA DE LISTAGEM DE DEPOIMENTOS
        return self::getTestimonies($request);
    }
}
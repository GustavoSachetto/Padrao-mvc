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
            'pagination' => parent::getPagination($request, $obPagination)
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Página depoimentos', $content, 'testimonies');
    }
}

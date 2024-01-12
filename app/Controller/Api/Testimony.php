<?php

namespace App\Controller\Api;

use Exception;
use App\Model\DatabaseManager\Pagination;
use App\Model\Entity\Testimony as EntityTestimony;

class Testimony extends Api
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
        $itens = [];

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
            $itens[] = [
                'id'       => $obTestimony->id,
                'nome'     => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data'     => $obTestimony->data
            ];
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por retornar os depoimentos cadastrados
     * @param Request $request
     * @return array
     */
    public static function getTestimonies($request)
    {
        return [
            'depoimentos' => self::getTestimonyItens($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar um depoimento individual
     * @param Request $request
     * @return array
     */
    public static function getTestimony($request, $id)
    {
        // VALIDA O ID DO DEPOIMENTO
        if (!is_numeric($id)) {
            throw new Exception("O id ".$id." não é válido.", 400);
        }
        // BUSCA DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        // VÁLIDA SE O DEPOIMENTO EXISTE
        if (!$obTestimony instanceof EntityTestimony) {
            throw new Exception("O depoimento ".$id." não foi encontrado.", 404);
            
        }

        // RETORNA OS DETALHES DO DEPOIMENTO
        return [
            'id'       => $obTestimony->id,
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data'     => $obTestimony->data
        ];
    }
}

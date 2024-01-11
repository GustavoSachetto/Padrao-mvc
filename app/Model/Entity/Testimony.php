<?php

namespace App\Model\Entity;

use App\Model\DatabaseManager\Database;

class Testimony
{
    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário que fez o depoimento
     *
     * @var string
     */
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data de publicação
     * @var string
     */
    public $data;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        // DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('depoimentos'))->insert([
            'nome'     => $this->nome,
            'mensagem' => $this->mensagem,
            'data'     => $this->data
        ]);

        return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instância atual
     * @return boolean
     */
    public function atualizar()
    {
        // ATUALIZA O DEPOIMENTO NO BANCO DE DADOS        
        return (new Database('depoimentos'))->update('id = '.$this->id, [
            'nome'     => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    /**
     * Método responsável por excluir um dado no banco com a instância atual
     * @return boolean
     */
    public function excluir()
    {
        // EXCLUÍ O DEPOIMENTO NO BANCO DE DADOS        
        return (new Database('depoimentos'))->delete('id = '.$this->id);
    }

    /**
     * Método que retorna os depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método reponsável por retornar um depoimento com base no seu ID
     * @param  integer $id
     * @return Testimony
     */
    public static function getTestimonyById($id)
    {
        return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }
}

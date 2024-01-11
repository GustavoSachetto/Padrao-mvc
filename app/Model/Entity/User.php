<?php

namespace App\Model\Entity;

use App\Model\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * Email do usuário
     * @var string
     */
    public $email;
    
    /**
     * Senha do usuario
     * @var string
     */
    public $senha;

    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        // INSERE O USUÁRIO NO BANCO DE DADOS
        $this->id = (new Database('usuarios'))->insert([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        return true;
    }

    /**
     * Método responsável por atualizar os dados do banco com a instância atual
     * @return boolean
     */
    public function atualizar()
    {
        // ATUALIZA O USUÁRIO NO BANCO DE DADOS        
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'nome'     => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    /**
     * Método responsável por excluir um dado no banco com a instância atual
     * @return boolean
     */
    public function excluir()
    {
        // EXCLUÍ O USUÁRIO NO BANCO DE DADOS        
        return (new Database('usuarios'))->delete('id = '.$this->id);
    }

    /**
     * Método que retorna os usuários cadastrados no banco
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método reponsável por retornar um usuário com base em seu e-mail
     * @param string $email
     * @return User
     */
    public static function getUserByEmail($email)
    {
        return (new Database('usuarios'))->select('email = "'.$email.'"')->fetchObject(self::class);
    }

    /**
     * Método reponsável por retornar um usuário com base no seu ID
     * @param  integer $id
     * @return User
     */
    public static function getUserById($id)
    {
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }
}

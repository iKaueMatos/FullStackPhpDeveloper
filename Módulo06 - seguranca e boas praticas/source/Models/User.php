<?php

namespace Source\Models;

// use Source\Database\Entity\UserEntity;
use Source\Core\Model;

/**
 * Class User
 * @package Source\Models
 */
class User extends Model
{
    /** @var array $safe no update or create */
    protected static $safe = ["id", "created_at", "updated_at"];

    /** @var string $entity database table */
    protected static $entity = "users";

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string|null $document
     * @return null|User
     * 
     * Ajuda a construir os dados necessários para poder cadastrar um novo usuário
     */
    public function bootstrap(string $firstName, string $lastName, string $email, string $document = null): ?User
    {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->email = $email;
        $this->document = $document;
        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return null|User
     * 
     * Buscando um usuário pelo id
     */
    public function load(int $id, string $columns = "*"): ?User
    {
        $load = $this->read("SELECT {$columns} FROM " . self::$entity . " WHERE id = :id", "id={$id}");

        if ($this->fail() || !$load->rowCount()) {
            $this->message = "Usuário não encontrado para o id informado";
            return null;
        }
        return $load->fetchObject(__CLASS__);
    }

    /**
     * @param $email
     * @param string $columns
     * @return null|User
     * 
     * Buscando um usuário pelo email
     */
    public function find($email, string $columns = "*"): ?User
    {
        $find = $this->read("SELECT {$columns} FROM " . self::$entity . " WHERE email = :email", "email={$email}");
        if ($this->fail() || !$find->rowCount()) {
            $this->message = "Usuário não encontrado para o email informado";
            return null;
        }
        return $find->fetchObject(__CLASS__);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $columns
     * @return array|null
     * 
     * Buscando todos os resultados
     * offset -> retorna os registros acima do anunciado
     */
    public function all(int $limit = 30, int $offset = 0, string $columns = "*"): ?array
    {
        $all = $this->read("SELECT {$columns} FROM " . self::$entity . " LIMIT :limit OFFSET :offset", "limit={$limit}&offset={$offset}");

        if ($this->fail() || !$all->rowCount()) {
            $this->message = "Sua consulta não retornou usuários";
            return null;
        }
        return $all->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return null|User
     * 
     * Responsável por cadastrar/salvar o novo usuário na base
     */
    public function save(): ?User
    {
        if (!$this->required()) {
            return null;
        }

        /** User Update */
        if (!empty($this->id)) {
            $userId = $this->id;

            $email = $this->read(
                "SELECT id FROM users WHERE email = :email AND id != :id",
                "email={$this->email}&id={$userId}"
            );

            if ($email->rowCount()) {
                $this->message = "O e-mail informado já está cadastrado";
                return null;
            }

            $this->update(self::$entity, $this->safe(), "id = :id", "id={$userId}");
            
            if ($this->fail()) {
                $this->message = "Erro ao atualizar, verifique os dados";
            }
            $this->message = "Dados atualizados com sucesso";
        }

        /** User Create */
        if(empty($this->id)) {
            if($this->find($this->email)) {
                $this->message = "O e-mail informado já está cadastrado!";
                return null;
            }

            $userId = $this->create(self::$entity, $this->safe());

            if ($this->fail()) {
                $this->message = "Erro ao cadastrar, verifique os dados";
            }

            $this->message = "Cadastro realizado com sucesso!";
        }

        $this->data = $this->read("SELECT * FROM users WHERE id = :id", "id={$userId}")->fetch();
        return $this;
    }

    /**
     * @return null|User
     * 
     * Deletando um usuário
     */
    public function destroy(): ?User
    {
        if(!empty($this->id)) {
            $this->delete(self::$entity, "id = :id", "id={$this->id}");
        }

        if($this->fail()) {
            $this->message = "Não foi possível remover o usuário!!!";
            return null;
        }

        $this->message = "Usuário removido com sucesso!!!";
        $this->data = null;

        return $this;
    }

    /**
     * @return bool
     * 
     * Diz quais campos o BD faz de obrigatório ser informado pelo usuário ao se cadastrar
     */
    private function required(): bool
    {
        if (empty($this->first_name) || empty($this->last_name) || empty($this->email)) {
            $this->message = "Nome, sobrenome e e-mail são obrigatórios";
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->message = "O e-mail informado não parece válido";
            return false;
        }

        return true;
    }
}
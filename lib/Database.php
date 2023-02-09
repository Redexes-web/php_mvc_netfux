<?php

namespace Lib;

class Database
{
    private $pdo;
    private $dsn;
    private $user;
    private $password;

    public function __construct()
    {
        $this->dsn = EnvLoader::get("DATABASE_URL");
        $this->user = EnvLoader::get("DATABASE_USERNAME");
        $this->password = EnvLoader::get("DATABASE_PASSWORD");
    }

    public function getPdo()
    {

        if ($this->pdo === null) {
            try {
                $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo 'Connexion échouée : ' . $e->getMessage();
            }
        }
        return $this->pdo;
    }
}

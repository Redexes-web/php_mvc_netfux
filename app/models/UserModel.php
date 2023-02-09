<?php

namespace App\Models;

use Lib\Utils;
use Lib\Session;
use \Lib\Database;
use App\Entity\User;

class UserModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = User::class;
        parent::__construct();
    }

    public function updateLastLogin($id)
    {
        $pdo = (new Database())->getPdo();
        $req = $pdo->prepare("UPDATE user SET LastLogin=NOW() WHERE Id=:id");
        $req->execute(['id' => $id]);
    }

    public function findByEmailAndCheck($email, $password)
    {
        $user = $this->findBy(["email" => $email]);
        if (empty($user)) {
            return null;
        }
        return password_verify($password, $user[0]->get('password')) ? $user[0] : null;
    }
}

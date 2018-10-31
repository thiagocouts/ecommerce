<?php

namespace Couts\Models;

use \Couts\DB\Sql;
use \Couts\Model;

class User extends Model
{
    const SESSION = "User";

    public static function login($login, $password)
    {
        $sql = new Sql;

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", [
            ":LOGIN" => $login
        ]);

        if (count($results) === 0) {
            throw new \Exception("Usuário ou senha inválido!", 1);
        }

        $data = $results[0];

        //verifica se os hashs são iguais
        if (password_verify($password, $data['despassword']) === true) {
            $user = new User;
            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();
            return $user;
        } else {
            throw new \Exception("Usuário ou senha inválido!", 1);
        }
    }

    public static function verifyLogin($inadmin = true)
    {
        if (!isset($_SESSION[User::SESSION])
            || !$_SESSION[User::SESSION]
            || !(int)$_SESSION[User::SESSION]['iduser'] > 0
            || (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin) {

            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout()
    {
        $_SESSION[User::SESSION] = null;
    }
}
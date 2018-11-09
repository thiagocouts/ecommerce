<?php

namespace Couts\Models;

use Couts\DB\Sql;
use Couts\Model;
use Couts\Mailer;

class User extends Model
{
    const SESSION = "User";
    const SECRET = "thiago_coutinho!";

    public static function getFromSession()
    {
        $user = new User;
        
        if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0) {
            $user->setData($_SESSION[User::SESSION]);
        }

        return $user;
    }

    public static function checkLogin($inadmin = true)
    {
        if (!isset($_SESSION[User::SESSION])
            || !$_SESSION[User::SESSION]
            || !(int)$_SESSION[User::SESSION]['iduser'] > 0) {
            //não tá logado
            return false;
        } else {
            if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {
                return true;
            } else if ($inadmin === false) {
                return true;
            } else {
                return false;
            }
        }
    }

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
        if (!User::checkLogin($inadmin)) {
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout()
    {
        $_SESSION[User::SESSION] = null;
    }

    public static function listAll()
    {
        $sql = new Sql;

        $user = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

        return $user;
    }

    public function store()
    {
        $sql = new Sql;

        $data = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", [
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ]);

        $this->setData($data[0]);

        return $data;
    }

    public function find($iduser)
    {
        $sql = new Sql;

        $user = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", [
            ":iduser" => $iduser
        ]);

        $this->setData($user[0]);
    }

    public function update()
    {
        $sql = new Sql;

        $user = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", [
            ":iduser" => $this->getiduser(),
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ]);

        $this->setData($user[0]);
    }

    public function delete()
    {
        $sql = new Sql;

        $sql->query("CALL sp_users_delete(:iduser)", [
            ":iduser" => $this->getiduser()
        ]);
    }

    public static function forgot($email)
    {
        $sql = new Sql;
        $results = $sql->select(
            "SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email",
            [
                ":email" => $email
            ]
        );

        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha!");
        } else {

            $data = $results[0];

            $res = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", [
                ":iduser" => $data["iduser"],
                ":desip" => $_SERVER["REMOTE_ADDR"]
            ]);

            if (count($res) === 0) {
                throw new \Exception("Não foi possível recuperar a senha!");
            } else {
                $user = $res[0];
                $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $cod = openssl_encrypt($user['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
                $code = base64_encode($iv . $cod);

                $link = "http://localhost:8080/admin/forgot/reset?code=" . $code;

                $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinição de senha", "forgot", [
                    "name" => $data["desperson"],
                    "link" => $link
                ]);

                $mailer->send();

                return $data;
            }

        }
    }

    public static function validForgotDecrypt($result)
    {
        $result = base64_decode($result);
        $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
        $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
        $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);

        //$idrecovery = Encryption::Decrypt($code);
        $sql = new Sql();

        $results = $sql->select("SELECT *
            FROM tb_userspasswordsrecoveries a
            INNER JOIN tb_users b USING(iduser)
            INNER JOIN tb_persons c USING(idperson)
            WHERE
            a.idrecovery = :idrecovery
            AND
            a.dtrecovery IS NULL
            AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();", array(
            ":idrecovery" => $idrecovery
        ));

        if (count($results) === 0) {
            throw new \Exception("Não foi possível recuperar a senha.");
        } else {
            return $results[0];
            exit;
        }
    }

    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE 
                    idrecovery = :idrecovery", [
            ":idrecovery" => $idrecovery
        ]);
    }

    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE 
                    iduser = :iduser", [
            ":password" => $password,
            ":iduser" => $this->getiduser()
        ]);
    }
}
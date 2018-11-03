<?php

namespace Couts\Models;

use Couts\DB\Sql;
use Couts\Model;
use Couts\Mailer;

class Category extends Model
{

    public static function all()
    {
        $sql = new Sql;
        $categories = $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

        return $categories;
    }

    public function store()
    {
        $sql = new Sql;

        $data = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", [
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory()
        ]);

        $this->setData($data[0]);

        return $data;
    }

    public function find($idcategory)
    {
        $sql = new Sql;

        $category = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
            ":idcategory" => $idcategory
        ]);

        $this->setData($category[0]);
    }

    public function update()
    {
        $sql = new Sql;

        $user = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", [
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory()
        ]);

        $this->setData($user[0]);
    }

    public function delete()
    {
        $sql = new Sql;
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
            ":idcategory" => $this->getidcategory()
        ]);
    }
}
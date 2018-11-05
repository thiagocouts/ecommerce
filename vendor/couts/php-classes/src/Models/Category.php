<?php

namespace Couts\Models;

use Couts\DB\Sql;
use Couts\Model;
use Couts\Mailer;
use Couts\Models\Product;

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
        Category::updateFile();

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
        Category::updateFile();
    }

    public function delete()
    {
        $sql = new Sql;
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
            ":idcategory" => $this->getidcategory()
        ]);

        Category::updateFile();
    }

    public static function updateFile()
    {
        $categories = Category::all();

        $html = [];

        foreach ($categories as $row) {
            array_push($html, '<li><a href="/categories/' . $row['idcategory'] . '">' . $row['descategory'] . '</a></li>');
        }

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode("", $html));
    }

    public function getProducts($related = true)
    {
        $sql = new Sql;

        if ($related) {
            return $sql->select(
                "SELECT * FROM tb_products WHERE idproduct IN(
                    SELECT a.idproduct FROM tb_products a
                    INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory)",
                [
                    ":idcategory" => $this->getidcategory()
                ]
            );
        } else {
            return $sql->select(
                "SELECT * FROM tb_products WHERE idproduct NOT IN(
                    SELECT a.idproduct FROM tb_products a
                    INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory)",
                [
                    ":idcategory" => $this->getidcategory()
                ]
            );
        }
    }

    public function getProductsPage($page = 1, $itemsPerPage = 8)
    {
        $start = ($page - 1) * $itemsPerPage;
        $sql = new Sql;

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS * FROM tb_products a
            INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
            INNER JOIN tb_categories c ON c.idcategory = b.idcategory
            WHERE c.idcategory = :idcategory 
            LIMIT $start, $itemsPerPage",
            [
                ":idcategory" => $this->getidcategory()
            ]
        );

        $total = $sql->select("SELECT FOUND_ROWS() as total");

        return [
            'data' => Product::checkList($results),
            'total' => (int)$total[0]['total'],
            'pages' => ceil($total[0]['total'] / $itemsPerPage)
        ];
    }

    public function addProduct(Product $product)
    {
        $sql = new Sql;
        $sql->query(" INSERT INTO tb_productscategories(idcategory, idproduct)
                VALUES(: idcategory ,
        : idproduct) ", [
            " : idcategory " => $this->getidcategory(),
            " idproduct " => $product->getidproduct()
        ]);
    }


    public function removeProduct(Product $product)
    {
        $sql = new Sql;
        $sql->query(" DELETE FROM tb_productscategories WHERE idcategory = : idcategory and idproduct = : idproduct ", [
            " : idcategory " => $this->getidcategory(),
            " idproduct " => $product->getidproduct()
        ]);
    }
}
<?php

namespace Couts\Models;

use Couts\DB\Sql;
use Couts\Model;

class Product extends Model
{

    public static function all()
    {
        $sql = new Sql;
        $products = $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

        return $products;
    }

    public static function checkList($list)
    {
        foreach ($list as &$row) {
            $p = new Product();
            $p->setData($row);

            $row = $p->getValues();
        }

        return $list;
    }

    public function store()
    {
        $sql = new Sql;

        $data = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", [
            ":idproduct" => $this->getidproduct(),
            ":desproduct" => $this->getdesproduct(),
            ":vlprice" => $this->getvlprice(),
            ":vlwidth" => $this->getvlwidth(),
            ":vlheight" => $this->getvlheight(),
            ":vllength" => $this->getvllength(),
            ":vlweight" => $this->getvlweight(),
            ":desurl" => $this->getdesurl()
        ]);

        $this->setData($data[0]);

        return $data;
    }

    public function find($idproduct)
    {
        $sql = new Sql;

        $product = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
            ":idproduct" => $idproduct
        ]);

        $this->setData($product[0]);
    }

    public function delete()
    {
        $sql = new Sql;

        if (file_exists($_SERVER["DOCUMENT_ROOT"] .
            DIRECTORY_SEPARATOR . "resource" .
            DIRECTORY_SEPARATOR . "site" .
            DIRECTORY_SEPARATOR . "img" .
            DIRECTORY_SEPARATOR . "products" .
            DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg")) {

            unlink($_SERVER["DOCUMENT_ROOT"] .
                DIRECTORY_SEPARATOR . "resource" .
                DIRECTORY_SEPARATOR . "site" .
                DIRECTORY_SEPARATOR . "img" .
                DIRECTORY_SEPARATOR . "products" .
                DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg");
        }

        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
            ":idproduct" => $this->getidproduct()
        ]);
    }

    public function checkPhoto()
    {
        if (file_exists($_SERVER["DOCUMENT_ROOT"] .
            DIRECTORY_SEPARATOR . "resource" .
            DIRECTORY_SEPARATOR . "site" .
            DIRECTORY_SEPARATOR . "img" .
            DIRECTORY_SEPARATOR . "products" .
            DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg")) {

            $url = "/resource/site/img/products/" . $this->getidproduct() . ".jpg";
        } else {
            $url = "/resource/site/img/product.jpg";
        }

        return $this->setdesphoto($url);
    }

    public function getValues()
    {
        $this->checkPhoto();
        $values = parent::getValues();

        return $values;
    }

    public function setPhoto($file)
    {
        $ext = explode('.', $file['name']);
        $ext = end($ext); //pegando a ultima posicao do array

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'gif':
                $image = imagecreatefromgif($file['tmp_name']);
                break;
        }

        $dist = $_SERVER["DOCUMENT_ROOT"] .
            DIRECTORY_SEPARATOR . "resource" .
            DIRECTORY_SEPARATOR . "site" .
            DIRECTORY_SEPARATOR . "img" .
            DIRECTORY_SEPARATOR . "products" .
            DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg";

        imagejpeg($image, $dist);
        imagedestroy($image);

        $this->checkPhoto();
    }
}
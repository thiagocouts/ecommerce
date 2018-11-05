<?php

use Couts\PageAdmin;
use Couts\Models\User;
use Couts\Models\Product;

$app->get('/admin/products', function () {
    User::verifyLogin();

    $products = Product::all();

    $page = new PageAdmin();

    $page->setTpl("products", [
        "products" => $products
    ]);
});

$app->get('/admin/products/create', function () {
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("products-create");
});

$app->post('/admin/products/store', function () {
    User::verifyLogin();

    $product = new Product;

    $product->setData($_POST);
    $product->store();

    header("Location: /admin/products");
    exit;
});

$app->get('/admin/products/delete/:idproduct', function ($idproduct) {
    User::verifyLogin();

    $product = new Product;

    $product->find((int)$idproduct);
    $product->delete();

    header("Location: /admin/products");
    exit;
});

$app->get('/admin/products/:idproduct/edit', function ($idproduct) {
    User::verifyLogin();

    $product = new Product;

    $product->find((int)$idproduct);
    $page = new PageAdmin();

    $page->setTpl("products-edit", [
        "product" => $product->getValues()
    ]);
});

$app->post('/admin/products/update/:idproduct', function ($idproduct) {
    User::verifyLogin();

    $product = new Product;

    $product->find((int)$idproduct);
    $product->setData($_POST);
    $product->store();
    $product->setPhoto($_FILES["file"]);

    header("Location: /admin/products");
    exit;
});
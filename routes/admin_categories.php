<?php

use Couts\PageAdmin;
use Couts\Models\User;
use Couts\Models\Category;
use Couts\Models\Product;

$app->get('/admin/categories', function () {

    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

    if($search != '') {
        $pagination = Category::getPageSearch($search, $page);
    } else {
        $pagination = Category::getPage($page);
    }

    $pages = [];

    for ($i=0; $i < $pagination['pages']; $i++) { 
        array_push($pages, [
            'href' => '/admin/categories?' . http_build_query([
                'page' => $i +1,
                'search' => $search
            ]),
            'text' => $i +1
        ]);
    }

    $page = new PageAdmin();

    $page->setTpl("categories", [
        'categories' => $pagination['data'],
        'search' => $search,
        'pages' => $pages
    ]);
});

$app->get('/admin/categories/create', function () {
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("categories-create");
});

$app->post('/admin/categories/store', function () {
    User::verifyLogin();

    $category = new Category;

    $category->setData($_POST);
    $category->store();

    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/delete/:idcategory', function ($idcategory) {
    User::verifyLogin();

    $category = new Category;

    $category->find((int)$idcategory);
    $category->delete();

    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/:idcategory', function ($idcategory) {
    User::verifyLogin();

    $category = new Category;

    $category->find((int)$idcategory);
    $page = new PageAdmin();

    $page->setTpl("categories-edit", [
        "category" => $category->getValues()
    ]);
});

$app->post('/admin/categories/update/:idcategory', function ($idcategory) {
    User::verifyLogin();

    $category = new Category;

    $category->find((int)$idcategory);
    $category->setData($_POST);
    $category->update();

    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/:idcategory/products', function ($idcategory) {
    User::verifyLogin();

    $category = new Category;

    $category->find((int)$idcategory);
    $page = new PageAdmin();

    $page->setTpl("categories-products", [
        "category" => $category->getValues(),
        "productsRelated" => $category->getProducts(),
        "productsNotRelated" => $category->getProducts(false)
    ]);
});

$app->get('/admin/categories/:idcategory/products/:idproduct/add', function ($idcategory, $idproduct) {
    User::verifyLogin();

    $category = new Category;
    $product = new Product;

    $category->find((int)$idcategory);
    $product->find((int)$idproduct);

    $category->addProduct($product);

    header("Location: /admin/categories/" . $idcategory . "/products");
    exit;
});

$app->get('/admin/categories/:idcategory/products/:idproduct/remove', function ($idcategory, $idproduct) {
    User::verifyLogin();

    $category = new Category;
    $product = new Product;

    $category->find((int)$idcategory);
    $product->find((int)$idproduct);

    $category->removeProduct($product);

    header("Location: /admin/categories/" . $idcategory . "/products");
    exit;
});
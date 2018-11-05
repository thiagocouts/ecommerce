<?php

use Couts\Page;
use Couts\Models\Category;
use Couts\Models\Product;

$app->get('/', function () {

    $page = new Page();
    $products = Product::all();

    $page->setTpl("index", [
        'products' => Product::checkList($products)
    ]);
});

$app->get('/categories/:idcategory', function ($idcategory) {

    $p = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    $category = new Category;

    $category->find((int)$idcategory);
    $page = new Page();

    $pagination = $category->getProductsPage($p);
    $pages = [];

    for ($i = 1; $i < $pagination['pages']; $i++) {
        array_push($pages, [
            'link' => '/categories/' . $category->getidcategory() . '?page=' . $i,
            'page' => $i
        ]);
    }

    $page->setTpl("category", [
        "category" => $category->getValues(),
        "products" => $pagination['data'],
        "pages" => $pages
    ]);
});
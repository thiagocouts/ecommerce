<?php

use Couts\Page;
use Couts\Models\Category;

$app->get('/', function () {

    $page = new Page();

    $page->setTpl("index");
});

$app->get('/categories/:idcategory', function ($idcategory) {

    $cat = new Category;

    $cat->find((int)$idcategory);
    $page = new Page();

    $page->setTpl("category", [
        "category" => $cat->getValues(),
        "products" => []
    ]);
});
<?php

use Couts\PageAdmin;
use Couts\Models\User;
use Couts\Models\Category;

$app->get('/admin/categories', function () {
    User::verifyLogin();

    $categories = Category::all();

    $page = new PageAdmin();

    $page->setTpl("categories", [
        "categories" => $categories
    ]);
});

$app->get('/admin/categories/create', function () {
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("categories-create");
});

$app->post('/admin/categories/store', function () {
    User::verifyLogin();

    $cat = new Category;

    $cat->setData($_POST);
    $cat->store();

    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/delete/:idcategory', function ($idcategory) {
    User::verifyLogin();

    $cat = new Category;

    $cat->find((int)$idcategory);
    $cat->delete();

    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/:idcategory/edit', function ($idcategory) {
    User::verifyLogin();

    $cat = new Category;

    $cat->find((int)$idcategory);
    $page = new PageAdmin();

    $page->setTpl("categories-edit", [
        "category" => $cat->getValues()
    ]);
});

$app->post('/admin/categories/update/:idcategory', function ($idcategory) {
    User::verifyLogin();

    $cat = new Category;

    $cat->find((int)$idcategory);
    $cat->setData($_POST);
    $cat->update();

    header("Location: /admin/categories");
    exit;
});

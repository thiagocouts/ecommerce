<?php

use Couts\PageAdmin;
use Couts\Models\User;

$app->get('/admin/users', function () {

    User::verifyLogin();
    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", [
        'users' => $users
    ]);
});

$app->get('/admin/users/create', function () {
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("create");
});

$app->get('/admin/users/delete/:iduser', function ($iduser) {
    User::verifyLogin();

    $user = new User();

    $user->find((int)$iduser);
    $user->delete();

    header("Location: /admin/users");
    exit;
});

$app->get('/admin/users/:iduser/edit', function ($iduser) {
    User::verifyLogin();

    $user = new User();

    $user->find((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("edit", [
        "user" => $user->getValues()
    ]);
});

$app->post('/admin/users/store', function () {
    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    // $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
    //     "cost" => 12
    // ]);
    $user->setData($_POST);
    $user->store();

    header("Location: /admin/users");
    exit;
});

$app->post('/admin/users/update/:iduser', function ($iduser) {
    User::verifyLogin();

    $user = new User();
    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    $user->find((int)$iduser);
    $user->setData($_POST);
    $user->update();

    header("Location: /admin/users");
    exit;
});
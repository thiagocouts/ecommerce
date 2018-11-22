<?php

use Couts\PageAdmin;
use Couts\Models\User;

$app->get('/admin/users/:iduser/password', function ($iduser) {
    User::verifyLogin();

    $user = new User();
    $user->find((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("users-password", [
        "user" => $user->getValues(),
        "msgError" => $user->getError(),
        "msgSuccess" => $user->getSuccess()
    ]);
});

$app->post('/admin/users/:iduser/password', function ($iduser) {
    User::verifyLogin();

    if(!isset($_POST['despassword']) || $_POST['despassword'] === "") {
        User::setError("O campo senha é obrigatório!");
        header("Location: /admin/users/$iduser/password");
        exit;
    }

    if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === "") {
        User::setError("O campo confirmação de senha é obrigatório!");
        header("Location: /admin/users/$iduser/password");
        exit;
    }

    if($_POST['despassword'] !== $_POST['despassword-confirm']) {
        User::setError("Nova senha deve ser igual a confirmação de senha");
        header("Location: /admin/users/$iduser/password");
        exit;
    }

    $user = new User();
    $user->find((int)$iduser);

    $user->setPassword(User::getPasswordHash($_POST['despassword']));

    User::setSuccess("Senha alterada com sucesso!");
    header("Location: /admin/users/$iduser/password");
    exit;
});

$app->get('/admin/users', function () {

    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

    if($search != '') {
        $pagination = User::getPageSearch($search, $page);
    } else {
        $pagination = User::getPage($page);
    }

    $page = new PageAdmin();

    $pages = [];

    for ($i=0; $i < $pagination['pages']; $i++) { 
        array_push($pages, [
            'href' => '/admin/users?' . http_build_query([
                'page' => $i +1,
                'search' => $search
            ]),
            'text' => $i +1
        ]);
    }

    $page->setTpl("users", [
        'users' => $pagination['data'],
        'search' => $search,
        'pages' => $pages
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

$app->get('/admin/users/:iduser', function ($iduser) {
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
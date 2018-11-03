<?php

session_start();

require_once("vendor/autoload.php");

use Slim\Slim;
use Couts\DB\Sql;
use Couts\Page;
use Couts\PageAdmin;
use Couts\Models\User;
use Couts\Models\Category;

$app = new Slim;
$app->config('debug', true);

$app->get('/', function () {

    $page = new Page();

    $page->setTpl("index");
});

$app->get('/admin', function () {
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");
});

$app->get('/admin/login', function () {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false
    ]);

    $page->setTpl("login");
});

$app->post('/admin/login', function () {

    User::login($_POST['login'], $_POST['password']);

    header("Location: /admin");
    exit;
});

$app->get('/admin/logout', function () {

    User::logout();

    header("Location: /admin/login");
    exit;
});

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
    $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
        "cost" => 12
    ]);
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

$app->get('/admin/forgot', function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("forgot");
});

$app->post('/admin/forgot', function () {

    $user = User::forgot($_POST['email']);

    header("Location: /admin/forgot/sent");
    exit;
});

$app->get('/admin/forgot/sent', function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("forgot-sent");
});

$app->get('/admin/forgot/reset', function () {

    $user = User::validForgotDecrypt($_GET['code']);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("forgot-reset", [
        'name' => $user['desperson'],
        'code' => $_GET["code"]
    ]);
});

$app->post('/admin/forgot/reset', function () {

    $forgot = User::validForgotDecrypt($_POST['code']);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();
    $user->find((int)$forgot["iduser"]);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
        "cost" => 12
    ]);

    $user->setPassword($password);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("forgot-reset-success");
});

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

$app->get('/categories/:idcategory', function ($idcategory) {

    $cat = new Category;

    $cat->find((int)$idcategory);
    $page = new Page();

    $page->setTpl("category", [
        "category" => $cat->getValues(),
        "products" => []
    ]);
});

$app->run();

?>
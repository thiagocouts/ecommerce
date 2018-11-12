<?php

use Couts\Page;
use Couts\Models\Category;
use Couts\Models\Product;
use Couts\Models\Cart;
use Couts\Models\Address;
use Couts\Models\User;

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

    $pagination = $category->getProductsPage($p);
    $pages = [];

    for ($i = 1; $i < $pagination['pages']; $i++) {
        array_push($pages, [
            'link' => '/categories/' . $category->getidcategory() . '?page=' . $i,
            'page' => $i
        ]);
    }

    $page = new Page();

    $page->setTpl("category", [
        "category" => $category->getValues(),
        "products" => $pagination['data'],
        "pages" => $pages
    ]);
});

$app->get('/products/:desurl', function ($desurl) {

    $product = new Product;
    $product->getFromUrl($desurl);

    $page = new Page();

    $page->setTpl("product-detail", [
        'product' => $product->getValues(),
        'categories' => $product->getCategories()
    ]);
});

$app->get('/cart', function () {

    $cart = Cart::getFromSession();
    $cart->checkZipCode();
    $page = new Page();

    $page->setTpl("cart", [
        "cart" => $cart->getValues(),
        "products" => $cart->getProducts(),
        "error" => Cart::getMsgError()
    ]);
});

$app->get('/cart/:idproduct/add', function ($idproduct) {

    $product = new Product();
    $product->find((int)$idproduct);

    $cart = Cart::getFromSession();
    $cart->addProduct($product);

    header("Location: /cart");
    exit;
});

$app->get('/cart/:idproduct/remove', function ($idproduct) {

    $product = new Product();
    $product->find((int)$idproduct);

    $cart = Cart::getFromSession();
    $cart->removeProduct($product);

    header("Location: /cart");
    exit;
});

$app->get('/cart/:idproduct/remove-all', function ($idproduct) {

    $product = new Product();
    $product->find((int)$idproduct);

    $cart = Cart::getFromSession();
    $cart->removeProduct($product, true);

    header("Location: /cart");
    exit;
});

$app->post('/cart/freight', function () {

    $cart = Cart::getFromSession();
    $cart->setFreight($_POST['zipcode']);

    header("Location: /cart");
    exit;
});

$app->get('/checkout', function () {

    User::verifyLogin(false);

    $cart = Cart::getFromSession();
    $address = new Address();
    $page = new Page();

    $page->setTpl("checkout", [
        "cart" => $cart->getValues(),
        "address" => $address->getValues()
    ]);
});

$app->get('/login', function () {

    $page = new Page();

    $page->setTpl("login", [
        'error' => User::getError()
    ]);
});

$app->post('/login', function () {

    try {
        User::login($_POST['login'], $_POST['password']);
    } catch(\Exception $e) {
        User::setError($e->getMessage());
    }

    header("Location: /checkout");
    exit;
});

$app->get('/logout', function () {

    User::logout();
    User::getError();
    Cart::removeToSession();
    session_regenerate_id();

    header("Location: /login");
    exit;
});


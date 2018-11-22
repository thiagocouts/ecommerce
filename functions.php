<?php

use Couts\Models\User;
use Couts\Models\Cart;

function formatPrice($vlprice)
{
    if (!$vlprice > 0) $vlprice = 0;
    
    return number_format($vlprice, 2, ",", ".");
}

function checkLogin($inadmin = true)
{
    return User::checkLogin($inadmin);
}

function getUserName()
{
    $user = User::getFromSession();

    return $user->getdesperson();
}

function getCartNrQtd()
{
    $cart = Cart::getFromSession();
    $total = $cart->getProductsTotals();

    return $total['nrqtd'];
}

function getCartVlSubTotal()
{
    $cart = Cart::getFromSession();
    $total = $cart->getProductsTotals();

    return formatPrice($total['vlprice']);
}

function formatDate($date)
{   
    return date('d/m/Y', strtotime($date));
}
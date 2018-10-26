<?php

require_once("vendor/autoload.php");

use Slim\Slim;
use Couts\DB\Sql;
use Couts\Page;

$app = new Slim;
$app->config('debug', true);

$app->get('/', function () {

    $page = new Page();
    $page->setTpl("index");
});

$app->run();

?>
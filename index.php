<?php

session_start();

require_once("vendor/autoload.php");

use Slim\Slim;
use Couts\Page;
use Couts\PageAdmin;
use Couts\Models\User;
use Couts\Models\Category;

$app = new Slim;
$app->config('debug', true);

require_once("routes/web.php");
require_once("routes/admin.php");
require_once("routes/admin_users.php");
require_once("routes/admin_categories.php");
require_once("routes/admin_products.php");

$app->run();

?>
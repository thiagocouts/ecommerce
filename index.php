<?php

require_once("vendor/autoload.php");

$app = new \Slim\Slim();
$app->config('debug', true);

$app->get('/', function () {

    $sql = new Couts\DB\Sql();
    $data = $sql->select("SELECT * FROM tb_users");

    echo json_encode($data);
});

$app->run();

?>
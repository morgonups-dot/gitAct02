<?php
session_start();

require_once __DIR__ . '/src/Request.php';
require_once __DIR__ . '/src/RandomGenerator.php';
require_once __DIR__ . '/src/Renderer.php';
require_once __DIR__ . '/src/App.php';

$request = new Request($_GET, $_POST);
$renderer = new Renderer();
$app = new App($request, $renderer);
$app->run();

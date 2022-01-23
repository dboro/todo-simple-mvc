<?php

session_start();


require __DIR__ . '/../vendor/autoload.php';

$db = require __DIR__ . '/../config/database.php';
$taskModel = new \App\Models\Task($db);

$view = require __DIR__ . '/../config/view.php';
$validator = new \Rakit\Validation\Validator();

$tasksController = new \App\Controllers\TasksController($view, $validator, $taskModel);

$router = new \Bramus\Router\Router();

$router->get('/', function () use ($tasksController) {
    $tasksController->index();
});

$router->get('/tasks/create', function () use ($tasksController) {
    $tasksController->create();
});

$router->post('/tasks/store', function () use ($tasksController) {
    $tasksController->store();
});

$router->get('/tasks/edit/{id}', function ($id) use ($tasksController) {
    $tasksController->edit($id);
});

$router->post('/tasks/update/{id}', function ($id) use ($tasksController) {
    $tasksController->update($id);
});

$siteController = new \App\Controllers\SiteController($validator, $view);

$router->get('/login', function () use ($siteController) {
    $siteController->login();
});

$router->post('/login', function () use ($siteController) {
    $siteController->signIn();
});

$router->get('/logout', function () use ($siteController) {
    $siteController->signOut();
});

$router->get('/error/{code}', function ($code) use ($siteController) {
    $siteController->error($code);
});

$router->set404(function () use ($siteController) {
    $siteController->error(404);
});

$router->run();
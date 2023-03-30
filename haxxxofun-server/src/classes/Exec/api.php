<?php

spl_autoload_register(function ($className) {
    include dirname(__DIR__) . '/' . str_replace('\\', '/', $className) . '.php';
});

use Handlers\AllResultsGetHandler;
use Handlers\IndexHandler;
use Handlers\SetResultHandler;
use Handlers\TasksResultsFinalGetHandler;
use Handlers\TasksResultsGetHandler;
use Repositories\ResultsRepository;
use Stooge\SetHeaderHandler;
use Stooge\Stooge;

$configs = include(dirname(__DIR__) . '../../../config/service.php');
$tasksConfigs = include(dirname(__DIR__) . '../../../config/tasks.php');

session_start();

try {
    $pdo = new PDO('mysql:host=' . $configs['db']['host'] .
        ';dbname=' . $configs['db']['name'],
        $configs['db']['username'],
        $configs['db']['password'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
} catch (Exception $e) {
    error_log("Failed on DB connection.");
    die(1);
}

$resultsRepository = new ResultsRepository($pdo);

$stooge = new Stooge();
$stooge
    ->get('/', new IndexHandler())
    ->get('/results/all', new AllResultsGetHandler($resultsRepository))
    ->get('/results/tasks', new TasksResultsGetHandler($resultsRepository))
    ->get('/results/final', new TasksResultsFinalGetHandler($resultsRepository))
    ->post('/result', new SetResultHandler($resultsRepository, $tasksConfigs['codes']))
    ->postHook(new SetHeaderHandler('Content-Type', 'application/json'));

if ($configs['cors']['enabled']) {
    $stooge
        ->options('/*', new IndexHandler())
        ->options('/*/*', new IndexHandler())
        ->options('/*/*/*', new IndexHandler())
        ->options('/*/*/*/*', new IndexHandler())
        ->options('/*/*/*/*/*', new IndexHandler())
        ->postHook(new SetHeaderHandler('Access-Control-Allow-Origin', $configs['cors']['origin']))
        ->postHook(new SetHeaderHandler('Access-Control-Allow-Methods', $configs['cors']['methods']))
        ->postHook(new SetHeaderHandler('Access-Control-Allow-Credentials', $configs['cors']['credentials']))
        ->postHook(new SetHeaderHandler('Access-Control-Allow-Headers', $configs['cors']['headers']));
}

$stooge->foolAround();
<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

use React\EventLoop\Timer\Timer;
use \React\Http\Request;
use \React\Http\Response;
use \React\EventLoop\Factory;
use \Voidcontext\Arc\Reactor\App;
use \Voidcontext\Arc\Reactor\Server\Adapter\ReactHttpServer;

use React\Promise\Deferred;
use React\ChildProcess\Process;

use \KHR\React\Curl\Curl;
use \KHR\React\Curl\Exception;

require __DIR__ . "/vendor/autoload.php";
require(__DIR__. "/config.php");

$startrun = date("Y-m-d H:i:s");
$loop = Factory::create();
$curl = new Curl($loop);
$server = new ReactHttpServer($loop);

$curl->client->enableHeaders();

$app = new App($server, [
    'port' => 4555
    ,
]);

$connection = new React\MySQL\Connection($loop, [
    'dbname' => $sv_config['dbname'],
    'user'   => $sv_config['user'],
    'passwd' => $sv_config['passwd'],
    'host'   => $sv_config['mysqlsv']
]);

$connection->connect(function () {});

function runPayload($payload) {
	global $loop;
	$result = '';
	$deferred = new Deferred();
	$process  = new Process($payload);

	$process->start($loop);

	$process->stdout->on('data', function ($chunk) use (&$result) {
		$result .= $chunk;
	});
	
	$process->on('error', function($e) use($deferred) {
		$deferred->reject($e);
	});

	$process->on('exit', function ($code, $term) use(&$result, $deferred) {

		$deferred->resolve($result);

	});

	return $deferred->promise();
}

function runSql($payload) {
	global $connection;
	$result = '';
	$deferred = new Deferred();

	$connection->query($payload, function ($command, $conn) use($deferred){
		if ($command->hasError()) {
			$error = $command->getError();
			$deferred->reject($error);
		} else {
			if(isset($command->resultRows)) {
				$results = $command->resultRows;
			}else {
				$results = true;
			}
			$deferred->resolve($results);
		}
	});
	return $deferred->promise();
}

$app->get('/', function (Request $request, Response $response) use($connection, $loop, &$startrun) {

    $results = [
        'app'   => 'cepsNatt',
        'bio'   => 'Coleta de informacoes apartir do CEP.',
        'dev'   => 'minprogramador',
        'status' => true,
        'start' => $startrun
    ];

    $response->writeHead(200, ["Content-Type" => "application/json"]);
    $response->write(json_encode($results));
    $response->end();
});

$app->get('/status/pendente', function (Request $request, Response $response) use($connection, $loop, &$startrun) {

	$connection->query('select count(cep) as total from ceps where `status`=1;', function ($command, $conn) use ($request, $loop, $response) {
		if ($command->hasError()) {
			$error = $command->getError();
		} else {
			$results = $command->resultRows;
		}
		$response->writeHead(200, ["Content-Type" => "application/json"]);
		$response->write(json_encode($results));
		$response->end();
	});

});

$app->get('/status/processados', function (Request $request, Response $response) use($connection, $loop, &$startrun) {

	$connection->query('select count(cep) as total from ceps where `status`=3;', function ($command, $conn) use ($request, $loop, $response) {
		if ($command->hasError()) {
			$error = $command->getError();
		} else {
		$results = $command->resultRows;
		}
		$response->writeHead(200, ["Content-Type" => "application/json"]);
		$response->write(json_encode($results));
		$response->end();
	});

});

$app->run();
$loop->run();

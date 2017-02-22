<?php 
require 'vendor/autoload.php';
	// Create and configure Slim app

$app = new \Slim\App();
//DB Connect
function getDB(){
	$dbhost = "mysql.hostinger.es";
	$dbname = "u128195766_test";
	$dbuser = "u128195766_wayo";
	$dbpass = "123456wayo";
	$mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
	$dbConnection = new PDO($mysql_conn_string,$dbuser,$dbpass);
	$dbConnection->exec("set names utf8");
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
}

$app->get('/', function ($request, $response, $args) {
    return $response->write("Welcome to slim");
});
// Define app routes
//$app->get('/hello/{name}', function ($request, $response, $args) {
//    return $response->write("Hello " . $args['name']);
//})->setArgument('name','World');

$app->get('/all', function ($request, $response, $args) {
	try {
		$db = getDB();
		//$response->write("DB connect success");
		$sth = $db ->prepare("SELECT * FROM data");
		$sth->execute();
		$datachargue = $sth->fetchAll(PDO::FETCH_ASSOC);
		if ($datachargue) {
			$response = $response->withJson($datachargue);
			$db = null; //cerrar conexión
		}
	} catch (PDOException $e) {
		$response->write('{"error":{"texto":'.$e->getMessage().'}}');
	}
	return $response;
});
//Reutilizamos función para recuperar datos por id
$app->get('/one/{id}', function ($request, $response, $args) {
	try {
		$db = getDB();
		//$response->write("DB connect success");
		$sth = $db ->prepare("SELECT * FROM data WHERE id=:id");
		$sth->bindParam(":id", $args["id"],PDO::PARAM_INT);
		$sth->execute();
		$datachargue = $sth->fetchAll(PDO::FETCH_ASSOC);
		if ($datachargue) {
			$response = $response->withJson($datachargue);
			$db = null; //cerrar conexión
		}
	} catch (PDOException $e) {
		$response->write('{"error":{"texto":'.$e->getMessage().'}}');
	}
	return $response;
});
//Reutilizamos función para actualizar datos en la db
//Para probarlo en vez de put usamos post ya que es compatible con todos los buscadores
$app->post('/update', function ($request, $response) { //No es necesario $args
	try {
		$db = getDB();
		$datarq = $request->getParams();
		//$response->write("DB connect success");
		$sth = $db ->prepare("UPDATE data SET nombres=?, apellidos=?, dni=? WHERE id=?");
		$sth->execute(array($datarq["nombres"],$datarq["apellidos"],$datarq["dni"],$datarq["id"]));
		$response->write('{"error":"Datos actualizados"}');
	} catch (PDOException $e) {
		$response->write('{"error":{"texto":'.$e->getMessage().'}}');
	}
	return $response;
});
//Reutilizamos función para insertar datos en la db
//Para probarlo en vez de put usamos post ya que es compatible con todos los buscadores
$app->post('/add', function ($request, $response) { //No es necesario $args
	try {
		$db = getDB();
		$datarq = $request->getParams();
		//$response->write("DB connect success");
		$sth = $db ->prepare("INSERT INTO socios (nombres, apellidos, dni) VALUES (?,?,?)");
		$sth->execute(array($datarq["nombres"],$datarq["apellidos"],$datarq["dni"]));
		$response->write('{"error":"Datos actualizados"}');
	} catch (PDOException $e) {
		$response->write('{"error":{"texto":'.$e->getMessage().'}}');
	}
	return $response;
});
//Reutilizamos función para eliminar datos por id
$app->get('/delete/{id}', function ($request, $response, $args) {
	try {
		$db = getDB();
		//$response->write("DB connect success");
		$sth = $db ->prepare("DELETE FROM data WHERE id=:id");
		$sth->bindParam(":id", $args["id"],PDO::PARAM_INT);
		$sth->execute();
		$response->write('{"error":"Registro eliminado"}');
	} catch (PDOException $e) {
		$response->write('{"error":{"texto":'.$e->getMessage().'}}');
	}
	return $response;
});
// Run app
$app->run();
 ?>
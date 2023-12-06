<?
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: text/html; charset=UTF-8');

date_default_timezone_set('America/Chihuahua');
session_start();
error_reporting(1);
ini_set('display_errors', '1');

require_once 'confi/db.php';
$db = new Conexion();

require_once 'confi/SGBD.php';

require_once 'confi/functions.php';
$Site = new Site();

//cookies session
$Site->Cookies();
if(isset($id_user)){
	if($Site->VerifySession($id_user) == 0){
		$consulta=SGBD::sql("SELECT * FROM sessions WHERE id_user='$id_user' order by id DESC LIMIT 1");
		$row =$consulta->fetch_array(MYSQLI_ASSOC);
		$code_session = srow['code_session'];
		setcookie('sesion', $code_session, time() + 86499);
	}
}

$fecha_actual=date("Y-m-d H:i:s");

if(isset($_SESSION['user'])){
	$username = $_SESSION['user'];
}

if(isset($_SESSION['id_user'])){
	$id_user = $_SESSION['id_user'];
	$my_rol = $Site->getDataId('id_rol', $id_user);
}else{
	$my_rol = 0;
}

if(isset($_SESSION['id_becado'])){
	$id_becado = $_SESSION['id_becado'];
}


$url_site = 'https://fundacionce.org';
?>

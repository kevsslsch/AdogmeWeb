<?
require_once 'params_conexion.php';
     
class Conexion extends mysqli{
	public function __construct(){
	parent::__construct(HOST,USER,PASS,DB);
	$this->query("SET NAMES 'utf8'");
	$this->connect_errno ? die('Error con la conexión') : $x = 'Conectado';
	#echo $x;
	unset($x);
	}
	public function recorrer($y){
		return mysqli_fetch_array($y);
	}
}

?>
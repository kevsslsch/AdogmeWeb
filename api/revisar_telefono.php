<?
	include("confi/core.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$fecha_actual=date("Y-m-d H:i:s");
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	$numero_telefono = $data['numeroTelefono'];

	$jsonresponse = array();
	
	if(!empty($numero_telefono)){
		
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE telefono='$numero_telefono';");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		
		if($total == 0){
			$jsonresponse = array( 'status' => false, 
                                   'message' => 'No existe el usuario ' . $numero_telefono);
		}else{	

			$sql = $db->query("SELECT * FROM usuarios WHERE telefono='$numero_telefono'");
			$data = $db->recorrer($sql);
		
			$jsonresponse = array( 'status' => true, 
                                   'message' => 'Ok', 
                                   'id_user' => $data['id'], 
                                   'numero_telefono' => $data['telefono'], 
                                   'nombre' => html_entity_decode($data['nombre']));
		}	
		
	}else{
		$jsonresponse = array( 'status' => false, 'message' => 'Hay campos vacíos, debes rellenar todo con carácteres válidos.');
	}
	
	echo json_encode($jsonresponse);

?>
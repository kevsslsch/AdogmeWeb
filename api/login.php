<?
	include("confi/core.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$fecha_actual=date("Y-m-d H:i:s");
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	$numero_telefono = $data['numeroTelefono'];
	$contrasena = $data['contrasena'];
	
	$jsonresponse = array();
	
	if(!empty($numero_telefono) && !empty($contrasena)){
			
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE telefono='$numero_telefono';");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		
		if($total == 0){
			$jsonresponse = array( 'status' => false, 'message' => 'No existe el celular ' . $numero_telefono . '');
		}else{	
		
			$sql = $db->query("SELECT * FROM usuarios WHERE telefono='$numero_telefono'");
			$data = $db->recorrer($sql);
			
			$id_user = $data['id'];
			
			if(password_verify($contrasena, $data['contrasena'])){	
				if($data['status'] == 0){ 
					$jsonresponse = array( 'status' => true, 'message' => 'Ok', 'id_user' => $data['id'], 'numero_telefono' => $data['telefono'], 'nombre' => html_entity_decode($data['nombre']));
					
				}else{
					$jsonresponse = array( 'status' => false, 'message' => 'El usuario ha sido dado de baja.');
				}
			}else{
				$jsonresponse = array( 'status' => false, 'message' => 'La contraseña no coincide.');
			}
		}	
		
	}else{
		$jsonresponse = array( 'status' => false, 'message' => 'Hay campos vacíos, debes rellenar todo con carácteres válidos.');
	}
	
	echo json_encode($jsonresponse);

?>
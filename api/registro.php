<?
	include("confi/core.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$fecha_actual=date("Y-m-d H:i:s");
	
	$data = json_decode(file_get_contents('php://input'), true);
	
    $nombre = $data['nombre'];
	$numero_telefono = $data['numeroTelefono'];
    $fecha_nacimiento = $data['fechaNacimiento'];
    $contrasena = $data['contrasena'];

	$jsonresponse = array();
	
	if(!empty($nombre) && !empty($numero_telefono) && !empty($fecha_nacimiento) && !empty($contrasena)){
			
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE telefono='$numero_telefono';");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		
		if($total == 0){
			$passHash = password_hash($contrasena, PASSWORD_BCRYPT);

			$db->query("INSERT INTO usuarios(nombre, telefono, contrasena, fecha_nacimiento, fecha_registro) VALUES('$nombre', '$numero_telefono', '$passHash', '$fecha_nacimiento', '$fecha_actual')");
			$id_user = $db->insert_id;
	
			if($id_user == 0){
				$jsonresponse = array( 'status' => false, 
									   'message' => 'No se pudo guardar el usuario por el error ' . $db->error);
			}else{	
			
				$jsonresponse = array( 'status' => true, 
									   'message' => 'Ok', 
									   'id_user' => "$id_user", 
									   'numero_telefono' => $numero_telefono, 
									   'nombre' => html_entity_decode($nombre));
			}	
		}else{
			$jsonresponse = array( 'status' => false, 
									'message' => 'Ya existe ese celular registrado');
		}
		
		
	}else{
		$jsonresponse = array( 'status' => false, 'message' => 'Hay campos vacíos, debes rellenar todo con carácteres válidos.');
	}
	
	echo json_encode($jsonresponse);

?>
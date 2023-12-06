<?
	include("confi/core.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$fecha_actual=date("Y-m-d H:i:s");
	
	$data = json_decode(file_get_contents('php://input'), true);
	
    $id_user = $data['id_user'];
	$nombre = $data['nombre'];
    $meses = $data['meses'];
    $raza = $data['raza'];
    $comentarios = $data['comentarios'];
    $url_foto = $data['url_foto'];
    
	$jsonresponse = array();
	
	if(!empty($id_user) && !empty($nombre) && !empty($meses) && !empty($url_foto)){
			
        $db->query("INSERT INTO adopciones(id_user, nombre, meses, raza, comentarios, url_foto, fecha_registro) VALUES('$id_user', '$nombre', '$meses', '$raza', '$comentarios', '$url_foto', '$fecha_actual')");
		$id_adopcion = $db->insert_id;

		if($id_adopcion == 0){
			$jsonresponse = array( 'status' => false, 
                                   'message' => 'No se pudo guardar la adopción por el error ' . $db->error);
		}else{	
		
			$jsonresponse = array( 'status' => true, 
                                   'message' => 'Ok');
		}	
		
	}else{
		$jsonresponse = array( 'status' => false, 'message' => 'Hay campos vacíos, debes rellenar todo con carácteres válidos.');
	}
	
	echo json_encode($jsonresponse);

?>
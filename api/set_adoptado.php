<?
	include("confi/core.php");

	$ip = $_SERVER['REMOTE_ADDR'];
	$fecha_actual=date("Y-m-d H:i:s");
	
	$data = json_decode(file_get_contents('php://input'), true);
	
    $id_user = $data['id_user'];
	$id_adopcion = $data['id_adopcion'];

	$jsonresponse = array();
	
	if(!empty($id_user) && !empty($id_adopcion)){
			
        $db->query("UPDATE adopciones SET adoptado = 1 WHERE id_user = '$id_user' AND id = '$id_adopcion' AND adoptado = 0");

		$jsonresponse = array( 'status' => true, 
                               'message' => 'Ok');
		
	}else{
		$jsonresponse = array( 'status' => false, 'message' => 'Hay campos vacíos, debes rellenar todo con carácteres válidos.');
	}
	
	echo json_encode($jsonresponse);

?>
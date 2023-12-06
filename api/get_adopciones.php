<?
	include("confi/core.php");
	
	$jsonresponse = array();
	$adopciones = array();

	$ok = false;
	
	$sql = $db->query("SELECT count(*) FROM adopciones WHERE adoptado = 0 AND status = 1 ORDER BY id DESC");
	$existe = $db->recorrer($sql);
	$total = $existe["count(*)"]; 
	
	if($total == 0){
		$jsonresponse = array( 'status' => false, 'message' => 'No existen adopciones.');
	}else{	
					
        $query = SGBD::sql("SELECT * FROM adopciones WHERE adoptado = 0 AND status = 1 ORDER BY id DESC");
		while($row =$query->fetch_array(MYSQLI_ASSOC)){
			array_push($adopciones, 
					   array(
							"id" => $row['id'],
							"id_user" => $row['id_user'],
                            "nombre_persona" => $Site->getDataId('nombre', $row['id_user']),
                            "telefono" => $Site->getDataId('telefono', $row['id_user']),
							"nombre" => $row['nombre'],
							"meses" => $row['meses'],
                            "raza" => $row['raza'],
                            "comentarios" => $row['comentarios'],
                            "url_foto" => $row['url_foto'],
                            "fecha_registro" => $row['fecha_registro']));
		}
		
		$ok = true;
		
	}	
	

	if($ok){
		echo json_encode(array(
					'status' => true, 
				    'message' => 'Ok', 
					'adopciones' => $adopciones));
	}else{
		echo json_encode($jsonresponse);
	}
	

?>
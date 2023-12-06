<?

class Site{
		
	function VerifyLogin(){
		if(isset($_SESSION['user'])){
			return 1;
		}else{
			return 0; // no existe sesion;
		}
	}
	
	function VerifyLoginBecado(){
		if(isset($_SESSION['id_becado'])){
			return 1;
		}else{
			return 0; // no existe sesion;
		}
	}
	
	function cleanv($valor){
		$valor = str_ireplace("SELECT","",$valor);
		$valor = str_ireplace("COPY","",$valor);
		$valor = str_ireplace("DELETE","",$valor);
		$valor = str_ireplace("DROP","",$valor);
		$valor = str_ireplace("DUMP","",$valor);
		$valor = str_ireplace("TRUNCATE","",$valor);
		$valor = str_ireplace("INSERT INTO","",$valor);
		$valor = str_ireplace("INSERT","",$valor);
		$valor = str_ireplace(" OR ","",$valor);
		$valor = str_ireplace("LIKE","",$valor);
		$valor = str_ireplace("--","",$valor);
		$valor = str_ireplace("^","",$valor);
		$valor = str_ireplace("[","",$valor);
		$valor = str_ireplace("]","",$valor);
		$valor = str_ireplace("=","",$valor);
		$valor = str_ireplace("&","",$valor);
		$valor = str_ireplace("<b>","",$valor);
		$valor = str_ireplace("<u>","",$valor);
		$valor = str_ireplace("<br>","",$valor);
		$valor = str_ireplace("</script>","",$valor);
		$valor = str_ireplace("</b>","",$valor);
		$valor = str_ireplace("</u>","",$valor);
		$valor = str_ireplace("<hr>","",$valor);
		$valor = str_ireplace("<br>","",$valor);
		$valor = str_ireplace("<a href","",$valor);
		$valor = str_ireplace("<script>","a",$valor);
		$valor = str_ireplace("/a>","",$valor);
		$valor = str_ireplace("<","",$valor);
		$valor = str_ireplace(">","",$valor);
		
		return htmlentities($valor);
	}
	
	function AddLog($action){
		$fecha_actual=date("Y-m-d H:i:s");
		$action = htmlentities($action);
		$db = new Conexion();
		
		$username = $_SESSION['user'];
		$myid = $this->getData('id', $username);
		
		$db->query("INSERT INTO logs (id_user,action,date) VALUES('$myid','$action','$fecha_actual')");
	}
	
	
	function generateCode($size){
		return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $size); 
	}
	
	function Notif($head,$text,$type){
		echo "<script type=\"text/javascript\">alert(\"$text\");</script>";
	}	
	
	function Alert($text){
		echo "<script type=\"text/javascript\">alert(\"$text\");</script>";
	}
	
	function sweetAlert($type, $title, $text){
		echo "<script>Swal.fire({
			  title: '$title',
			  text: '$text',
			  icon: '$type',
			  confirmButtonColor: '#3085d6',
			  confirmButtonText: 'Aceptar'
		});</script>";
	}	
	
	function sweetAlertRedirect($type, $title, $text, $url){
		echo "<script>Swal.fire({
			  title: '$title',
			  text: '$text',
			  icon: '$type',
			  confirmButtonColor: '#3085d6',
			  confirmButtonText: 'Aceptar'
		}).then(function() {
			window.location.href = '$url';
		});</script>";		
	}
		
	function UserExist($user){
			
		$db = new Conexion();
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE user='$user'; ");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		if($total == 0){
			return 0; // 0 es que no existe	
		}else{
			return 1;  // 1 que si existe
		}
	}
	
	function Login($user, $pass){
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$fecha_actual=date("Y-m-d H:i:s");
		
		$db = new Conexion();
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE usuario='$user'; ");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		if($total == 0){
			$this->sweetAlert("error", "Error", "No existe el usuario $user");
		}else{
		
		$sql = $db->query("SELECT id, usuario, contrasena, id_rol, status FROM usuarios WHERE usuario='$user'");
		$data = $db->recorrer($sql);
		
		$id_user = $data['id'];
		
		if(password_verify($pass, $data['contrasena'])){	
			if($data['status'] == 0){ 
				session_start();
				$_SESSION['user'] = $user;
				$_SESSION['id_user'] = $id_user;
				
				$this->AddLog("Inició sesión en la Web");
				$db->query("INSERT INTO ips_login (id_user,ip,date) VALUES ('$id_user','$ip','$fecha_actual')");
				$db->query("UPDATE usuarios SET ip_last_login = '$ip' WHERE usuario='$user'");
				
				$this->addSession();
				$this->goWeb("index");
				
			}else{
				$this->sweetAlert("error", "Error", "El usuario ha sido dado de baja");
			}
		}else{
			$this->sweetAlert("error", "Error", "La contraseña no coincide.");
		}
     }	
	}
	
	function LoginBecado($user, $pass){
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$fecha_actual=date("Y-m-d H:i:s");
		
		$db = new Conexion();
		$sql = $db->query("SELECT count(*) FROM becados WHERE folio_ce='$user'; ");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		if($total == 0){
			$this->sweetAlert("error", "Error", "No existe el becado $user");
		}else{
		
		$sql = $db->query("SELECT id, folio_ce, contrasena, status FROM becados WHERE folio_ce='$user'");
		$data = $db->recorrer($sql);
		
		$id_becado = $data['id'];
		
		if(password_verify($pass, $data['contrasena'])){	
			if($data['status'] == 0){ 
				$_SESSION['id_becado'] = $id_becado;
				
				$db->query("UPDATE becados SET ip_last_login = '$ip' WHERE folio_ce='$user'");
				$this->goWebPublica("becados");
				
			}else{
				$this->sweetAlert("error", "Error", "El becado ha sido dado de baja");
			}
		}else{
			$this->sweetAlert("error", "Error", "La contraseña no coincide.");
		}
     }	
	}
	
	function addSession(){
		$db = new Conexion();
		session_start();
		$id_user = $_SESSION['id_user'];	

		$code_session = $this->generateCode(20);
		
		$db->query("DELETE FROM sessions WHERE id_user='$id_user'");
		setcookie('sesion', "", time() - 1); // borramos la cookies local
		
		$fecha_actual=date("Y-m-d H:i:s");
		$sum_seconds_fa = strtotime('+360 days', strtotime($fecha_actual));
		$nueva_caducidad = date ('Y-m-d H:i:s', $sum_seconds_fa);
		
		$db->query("INSERT INTO sessions (id_user,code_session,date_expiration) VALUES('$id_user', '$code_session','$nueva_caducidad')");

		setcookie('sesion', $code_session, time() + 31104000);	
	}
	
	
	function VerifySession($id_user){

		$db = new Conexion();
		session_start();
		$consulta=SGBD::sql("SELECT * FROM sessions WHERE id_user='$id_user'");
		$row =$consulta->fetch_array(MYSQLI_ASSOC);
		$row_cnt = $row["count(*)"]; 
		$date_expiration = $row['date_expiration'];
		$fecha_actual_time=time(date("Y-m-d H:i:s"));

		$date_expiration_time = time($date_expiration);
		if(/*$fecha_actual_time > $date_expiration_time or */$row_cnt == 0){
			return 0; // ya no existe
		}else{
			return 1; //aun existe
		}
	}
	
	
	function Cookies(){
		//session_start();
		
		if(!isset($_SESSION['id_user'])){
			if(isset($_COOKIE['sesion'])){
				$code_session = $_COOKIE['sesion'];
				$consulta=SGBD::sql("SELECT * FROM sessions WHERE code_session='$code_session'");
				$row =$consulta->fetch_array(MYSQLI_ASSOC);
				$date_expiration = $row['date_expiration'];
				$id_user_cookie = $row['id_user'];
				$user_cookie = $this->getDataId('usuario', $id_user_cookie);
				
				$fecha_actual=date("Y-m-d H:i:s");
				
				if($date_expiration > $fecha_actual){					
					$_SESSION['id_user'] = $id_user_cookie;
					$_SESSION['user'] = $user_cookie;
				}
			}
		}
		
	}
	
	function Register($user, $name, $pass, $rol, $url){
		$db = new Conexion();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$fecha_actual=date("Y-m-d H:i:s");
		
		$user = htmlentities($user);
		$name = htmlentities($name);
		$digitos_pass = strlen($pass);
		
		if($digitos_pass < 9){
			$var_p = 1;	
		}else{
			$var_p = 0;
		}
		
		//$date_birthday = $ybirthday . '-' . $mbirthday . '-' . $dbirthday;
		
		$passHash = password_hash($pass, PASSWORD_BCRYPT);
		$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
		$digitos = strlen($user);
		
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE usuario='$user'; ");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		
		for ($i=0; $i<strlen($user); $i++){ 
			if (strpos($permitidos, substr($user,$i,1))===false){ 
					$var = 1;
			}
		}
		
		if(empty($url)){
			$url = "https://fundacionce.org/admin/assets/images//users/no-avatar.png";
		}
		
		if($var_p == 1){
			$this->sweetAlert('error', '¡Ups!','La contraseña debe tener más de 8 carácteres');
		}else if($var == 1){
			$this->sweetAlert('error', '¡Ups!','El usuario contiene carácteres no permitidos');
		}else if($total > 0){
			$this->sweetAlert('error', '¡Ups!','Ese usuario ya ha sido registrado.');
		}else if($digitos > 12 or $digitos < 3){
			$this->sweetAlert('error', '¡Ups!','El nombre de usuario debe tener más de 2 digitos y menos de 12');	
		}else{
			$query = "INSERT INTO usuarios (usuario, nombre, contrasena, id_rol, url_image, fecha_registro) VALUES('$user', '$name', '$passHash', '$rol', '$url', '$fecha_actual')";
			
			$db->query($query);
			//$this->sweetAlert('success', '¡Bien!','Cuenta creada con éxito');
			$this->goWeb("usuarios");
		}	
	}
	
	function registraBecado($folio_ce, $nombres, $apellidos, $semestre_curso, $semestres_totales, $cuenta_banco, $total_pagos_beca, $total_beca, $total_devolucion_programada, $id_universidad, $id_carrera, $id_generacion, $id_ciclo_escolar, $id_proyecto, $id_proyecto2, $curp, $rfc, $fecha_inicio, $fecha_fin, $comentarios, $url_avatar){
		$db = new Conexion();
		$fecha_actual=date("Y-m-d H:i:s");
		
		/*$sql = $db->query("SELECT count(*) FROM becados WHERE folio_ce = '$folio_ce'");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
		
		if($total > 0){
			$this->sweetAlert("error", "Error al registrar", "El folio CE $folio_ce ya está registrado, por favor revísalo.");
		}else{*/
			$query = "INSERT INTO expedientes(id_universidad, id_carrera, nombres, apellidos, curp, fecha_registro) VALUES( '$id_universidad', '$id_carrera', '$nombres', '$apellidos', '$curp', '$fecha_actual')";
			$db->query($query);
			$id_expediente = $db->insert_id;
			
			if(empty($url_avatar)){
				$url_avatar = "https://fundacionce.org/admin/assets/images//users/no-avatar.png";
			}
			
			$query = "INSERT INTO becados(folio_ce, id_expediente, id_generacion, id_ciclo_escolar, id_proyecto, id_proyecto2, rfc, fecha_inicio, fecha_fin, comentarios, semestre_curso, semestres_totales, cuenta_banco, total_pagos_beca, total_beca, total_devolucion_programada, url_avatar, fecha_registro) VALUES( '0', '$id_expediente', '$id_generacion', '$id_ciclo_escolar', '$id_proyecto', '$id_proyecto2', '$rfc', '$fecha_inicio', '$fecha_fin', '$comentarios', '$semestre_curso', '$semestres_totales', '$cuenta_banco', '$total_pagos_beca', '$total_beca', '$total_devolucion_programada', '$url_avatar', '$fecha_actual')";
			$db->query($query);
			$id_becado = $db->insert_id;
		
			$db->query("UPDATE becados SET folio_ce = CONCAT('DEV 0', id) WHERE id = '$id_becado'");
			
			$this->AddLog("Registró al becado $nombres $apellidos con Folio CE $folio_ce");
			
			if(!empty($db->error)){
				$this->sweetAlert("success", "Error", "Se registró un error al capturar un becado: " . $db->error);
			}else{
				$this->goWeb("becados");
				$this->sweetAlert("success", "Éxito", "Se ha registrado con éxito el becado $nombres con folio CE $folio_ce (#$id_becado)");
			}
			
	//	}
	}
	
	function actualizaBecado($id, $nombres, $apellidos, $semestre_curso, $semestres_totales, $cuenta_banco, $total_pagos_beca, $total_beca, $total_devolucion_programada, $id_universidad, $id_carrera, $id_generacion, $id_ciclo_escolar, $id_proyecto, $id_proyecto2, $curp, $rfc, $fecha_inicio, $fecha_fin, $comentarios, $url_avatar){
		$db = new Conexion();
		$fecha_actual=date("Y-m-d H:i:s");
		
		$id_expediente = $this->getBecado('id_expediente', $id);
		$folio_ce = $this->getBecado('folio_ce', $id);
		
		$query = "UPDATE expedientes SET id_universidad = '$id_universidad', 
										 id_carrera = '$id_carrera', 
										 nombres = '$nombres', 
										 apellidos = '$apellidos',
										 curp = '$curp'
				  WHERE id = '$id_expediente'";

		$db->query($query);
		$id_expediente = $db->insert_id;
		
		
		$query = "UPDATE becados SET id_generacion = '$id_generacion', id_ciclo_escolar = '$id_ciclo_escolar', id_proyecto = '$id_proyecto', id_proyecto2 = '$id_proyecto2', rfc = '$rfc', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin', comentarios = '$comentarios', semestre_curso = '$semestre_curso', semestres_totales = '$semestres_totales', cuenta_banco = '$cuenta_banco', total_pagos_beca = '$total_pagos_beca', total_beca = '$total_beca', total_devolucion_programada = '$total_devolucion_programada' WHERE id = '$id'";
		$db->query($query);
		
		if(!empty($url_avatar)){
			$db->query("UPDATE becados SET url_avatar = '$url_avatar' WHERE id = '$id'");
		}
		
		$this->AddLog("Actualizó al becado $nombres $apellidos con Folio CE $folio_ce");
		
		$this->sweetAlert("success", "Éxito", "Se ha actualizado con éxito el becado $nombres con folio CE $folio_ce (#$id)");
	}
	
	function registraSolicitudBeca($nombres, $apellidos, $fecha_nacimiento, $sexo, $id_preparatoria_origen, $preparatoria_turno, $id_universidad, $id_carrera, $semestre_curso, $id_entero_beca,
								   $telefono_propio, $telefono_mama, $telefono_otro, 
								   $calle, $numero_exterior, $codigo_postal, $colonia, $ciudad, 
								   $acta_nacimiento, $ultima_boleta, $carta_mano, $ensayo, $foto, $horario_reunion){
		
		global $db, $fecha_actual;
				
		$nombres = $this->cleanv($nombres);
		$apellidos = $this->cleanv($apellidos);
		$semestre_curso = $this->cleanv($semestre_curso);
		$telefono_propio = $this->cleanv($telefono_propio);
		$telefono_mama = $this->cleanv($telefono_mama);
		$telefono_otro = $this->cleanv($telefono_otro);
		$calle = $this->cleanv($calle);
		$numero_exterior = $this->cleanv($numero_exterior);
		$codigo_postal = $this->cleanv($codigo_postal);
		$ciudad = $this->cleanv($ciudad);
		
		$query = "INSERT INTO expedientes(nombres, apellidos, fecha_nacimiento, sexo, id_preparatoria_origen, preparatoria_turno, id_universidad, id_carrera, semestre_curso_aspirante, id_entero_beca,
										  telefono_propio, telefono_mama, telefono_otro, 
										  calle, numero_exterior, codigo_postal, colonia, ciudad, horario_reunion,
										  fecha_registro) 
				   VALUES( '$nombres', '$apellidos', '$fecha_nacimiento', '$sexo', '$id_preparatoria_origen', '$preparatoria_turno', '$id_universidad', '$id_carrera', '$semestre_curso', '$id_entero_beca',
						   '$telefono_propio', '$telefono_mama', '$telefono_otro',
						   '$calle', '$numero_exterior', '$codigo_postal', '$colonia', '$ciudad', '$horario_reunion',
						   '$fecha_actual')";
						   
		$db->query($query);
		$id_expediente = $db->insert_id;
		
		$query = "INSERT INTO aspirantes(id_expediente, id_generacion, fecha_registro) VALUES( '$id_expediente', '11', '$fecha_actual')";
		$db->query($query);
		$id_aspirante = $db->insert_id;
		
		$url_acta_nacimiento = $this->uploadFile($acta_nacimiento, "assets/expedientes/$id_expediente/", "ACTA DE NACIMIENTO");
		$url_ultima_boleta = $this->uploadFile($ultima_boleta, "assets/expedientes/$id_expediente/", "ULTIMA BOLETA PREPARATORIA");
		$url_carta_mano = $this->uploadFile($carta_mano, "assets/expedientes/$id_expediente/", "CARTA MANO");
		$url_ensayo = $this->uploadFile($ensayo, "assets/expedientes/$id_expediente/", "ENSAYO");
		$url_foto = $this->uploadFile($foto, "assets/expedientes/$id_expediente/", "FOTO");
		
		$db->query("UPDATE expedientes SET url_acta_nacimiento = '$url_acta_nacimiento',
										   url_boleta_preparatoria = '$url_ultima_boleta',
										   url_ensayo = '$url_ensayo',
										   url_carta = '$url_carta_mano',
										   url_foto = '$url_foto'
					WHERE id = $id_expediente");
		
		if(!empty($db->error)){
			$this->sweetAlert("success", "Error", "Se registró un error al capturar la solicitud: " . $db->error);
		}else{
			$this->sweetAlert("success", "Éxito", "Se ha registrado con éxito la solicitud de beca con folio $id_aspirante");
		}
	}
	
	function registraSolicitudBecaAmerican($nombres, $apellidos, $fecha_nacimiento, $sexo, $id_preparatoria_origen, $preparatoria_turno, $id_universidad, $id_carrera, $semestre_curso,
								   $familiar_ofrecio, $otro_familiar_ofrecio, $nombre_familiar_ofrecio, $empresa_familiar, $otra_empresa_familiar, 
								   $telefono_propio, $telefono_mama, $telefono_otro, 
								   $calle, $numero_exterior, $codigo_postal, $colonia, $ciudad, 
								   $acta_nacimiento, $ultima_boleta, $carta_mano, $ensayo, $foto, $horario_reunion){
		
		global $db, $fecha_actual;
				
		$nombres = $this->cleanv($nombres);
		$apellidos = $this->cleanv($apellidos);
		$semestre_curso = $this->cleanv($semestre_curso);
		$telefono_propio = $this->cleanv($telefono_propio);
		$telefono_mama = $this->cleanv($telefono_mama);
		$telefono_otro = $this->cleanv($telefono_otro);
		$calle = $this->cleanv($calle);
		$numero_exterior = $this->cleanv($numero_exterior);
		$codigo_postal = $this->cleanv($codigo_postal);
		$ciudad = $this->cleanv($ciudad);
		
		$query = "INSERT INTO expedientes(nombres, apellidos, fecha_nacimiento, sexo, id_preparatoria_origen, preparatoria_turno, id_universidad, id_carrera, semestre_curso_aspirante,
										  familiar_ofrecio, otro_familiar_ofrecio, nombre_familiar_ofrecio, empresa_familiar, otra_empresa_familiar,
										  telefono_propio, telefono_mama, telefono_otro, 
										  calle, numero_exterior, codigo_postal, colonia, ciudad, horario_reunion,
										  fecha_registro) 
				   VALUES( '$nombres', '$apellidos', '$fecha_nacimiento', '$sexo', '$id_preparatoria_origen', '$preparatoria_turno', '$id_universidad', '$id_carrera', '$semestre_curso',
				  	  		'$familiar_ofrecio', '$otro_familiar_ofrecio', '$nombre_familiar_ofrecio', '$empresa_familiar', '$otra_empresa_familiar', 
				   			'$telefono_propio', '$telefono_mama', '$telefono_otro',
						    '$calle', '$numero_exterior', '$codigo_postal', '$colonia', '$ciudad', '$horario_reunion',
						    '$fecha_actual')";
						   
		$db->query($query);
		$id_expediente = $db->insert_id;
		
		$query = "INSERT INTO aspirantes(id_expediente, id_generacion, observaciones, fecha_registro) VALUES( '$id_expediente', '12', '$comentarios_american', '$fecha_actual')";
		$db->query($query);
		$id_aspirante = $db->insert_id;
		
		$url_acta_nacimiento = $this->uploadFile($acta_nacimiento, "assets/expedientes/$id_expediente/", "ACTA DE NACIMIENTO");
		$url_ultima_boleta = $this->uploadFile($ultima_boleta, "assets/expedientes/$id_expediente/", "ULTIMA BOLETA PREPARATORIA");
		$url_carta_mano = $this->uploadFile($carta_mano, "assets/expedientes/$id_expediente/", "CARTA MANO");
		$url_ensayo = $this->uploadFile($ensayo, "assets/expedientes/$id_expediente/", "ENSAYO");
		$url_foto = $this->uploadFile($foto, "assets/expedientes/$id_expediente/", "FOTO");
		
		$db->query("UPDATE expedientes SET url_acta_nacimiento = '$url_acta_nacimiento',
										   url_boleta_preparatoria = '$url_ultima_boleta',
										   url_ensayo = '$url_ensayo',
										   url_carta = '$url_carta_mano',
										   url_foto = '$url_foto'
					WHERE id = $id_expediente");
		
		if(!empty($db->error)){
			$this->sweetAlert("success", "Error", "Se registró un error al capturar la solicitud: " . $db->error);
		}else{
			$this->sweetAlert("success", "Éxito", "Se ha registrado con éxito la solicitud de beca con folio $id_aspirante");
		}
	}

	function registraSolicitudBecaMunicipio($nombres, $apellidos, $fecha_nacimiento, $sexo, $id_preparatoria_origen, $preparatoria_turno, $id_universidad, $id_carrera, $semestre_curso, $id_entero_beca,
								   $telefono_propio, $telefono_mama, $telefono_otro, 
								   $calle, $numero_exterior, $codigo_postal, $colonia, $ciudad, 
								   $lugar_familia, $otras_becas_universidad_sql, $primero_en_universidad, $quien_cursando_universidad_sql, $quien_termino_universidad_sql, $casa,
								   $tipo_casa, $casa_recamaras, $casa_cuartos, $personas_casa, $casa_banos, $casa_quien_vive_sql, $ingreso_familiar, $numero_personas_trabajan, 
								   $trabaja, $lugar_trabajo,
								   $familiares_id, $familiares_nombre, $familiares_parentezco, $familiares_edad, $familiares_telefono, $familiares_ocupacion, $familiares_escolaridad, $familiares_sueldo, $familiares_maquila, 
								   $referencias_id, $referencias_nombre, $referencias_parentezco, $referencias_telefono,
								   $como_eres, $pasatiempos_becado, $relacion_familiar, $porque_necesitas_beca,
								   $acta_nacimiento, $ultima_boleta, $carta_mano, $ensayo, $foto, $horario_reunion){
		
		global $db, $fecha_actual;
				
		$nombres = $this->cleanv($nombres);
		$apellidos = $this->cleanv($apellidos);
		$semestre_curso = $this->cleanv($semestre_curso);
		$telefono_propio = $this->cleanv($telefono_propio);
		$telefono_mama = $this->cleanv($telefono_mama);
		$telefono_otro = $this->cleanv($telefono_otro);
		$calle = $this->cleanv($calle);
		$numero_exterior = $this->cleanv($numero_exterior);
		$codigo_postal = $this->cleanv($codigo_postal);
		$ciudad = $this->cleanv($ciudad);
		
		$query = "INSERT INTO expedientes(nombres, apellidos, fecha_nacimiento, sexo, id_preparatoria_origen, preparatoria_turno, id_universidad, id_carrera, semestre_curso_aspirante, id_entero_beca,
										  telefono_propio, telefono_mama, telefono_otro, 
										  calle, numero_exterior, codigo_postal, colonia, ciudad, horario_reunion,
										  fecha_registro) 
				   VALUES( '$nombres', '$apellidos', '$fecha_nacimiento', '$sexo', '$id_preparatoria_origen', '$preparatoria_turno', '$id_universidad', '$id_carrera', '$semestre_curso', '$id_entero_beca',
						   '$telefono_propio', '$telefono_mama', '$telefono_otro',
						   '$calle', '$numero_exterior', '$codigo_postal', '$colonia', '$ciudad', '$horario_reunion',
						   '$fecha_actual')";
						   
		$db->query($query);
		$id_expediente = $db->insert_id;
		
		$query = "INSERT INTO aspirantes(id_expediente, id_generacion, fecha_registro) VALUES( '$id_expediente', '15', '$fecha_actual')";
		$db->query($query);
		$id_aspirante = $db->insert_id;
		
		$db->query("UPDATE familiares_expedientes SET status = 1 WHERE id_expediente = '$id_expediente'");
		$db->query("UPDATE referencias_expedientes SET status = 1 WHERE id_expediente = '$id_expediente'");
				   
		if(!is_null($familiares_id)){
			  for($count = 0; $count < count($familiares_id); $count++){
				  
				  $id_familiar = $familiares_id[$x];
				  
				  $db->query("INSERT INTO familiares_expedientes(id_expediente, nombre, parentezco, edad, telefono, ocupacion, escolaridad, sueldo, maquila) 
						 VALUES ($id_expediente, '$familiares_nombre[$count]', '$familiares_parentezco[$count]', '$familiares_edad[$count]', '$familiares_telefono[$count]', '$familiares_ocupacion[$count]', '$familiares_escolaridad[$count]', '$familiares_sueldo[$count]', '$familiares_maquila[$count]')");
						 
				 if(!empty($db->error)){
					 $message = $db->error;					
				 }
			 }
		 }

		 if(!is_null($referencias_id)){
			  for($count = 0; $count < count($referencias_id); $count++){
				  
				  $id_referencia = $referencias_id[$x];
				  
				  $db->query("INSERT INTO referencias_expedientes(id_expediente, nombre, parentezco, telefono) 
						 VALUES ($id_expediente, '$referencias_nombre[$count]', '$referencias_parentezco[$count]', '$referencias_telefono[$count]')");
						 
				 if(!empty($db->error)){
					 $message = $db->error;					
				 }
			 }
		}


		$db->query("UPDATE expedientes SET lugar_familia = '$lugar_familia',
										   otras_becas_universidad = '$otras_becas_universidad_sql',
										   primero_en_universidad = '$primero_en_universidad',
										   quien_cursando_universidad = '$quien_cursando_universidad_sql',
										   quien_termino_universidad = '$quien_termino_universidad_sql',
										   casa = '$casa',
										   tipo_casa = '$tipo_casa',
										   casa_recamaras = '$casa_recamaras',
										   casa_cuartos = '$casa_cuartos',
										   numero_personas_casa = '$personas_casa',
										   casa_banos = '$casa_banos',
										   casa_quien_vive = '$casa_quien_vive_sql',
										   ingreso_familiar_total = '$ingreso_familiar',
										   numero_personas_trabajan = '$numero_personas_trabajan',
										   trabaja = '$trabaja',
										   lugar_trabajo = '$lugar_trabajo',
										   como_eres = '$como_eres',
										   pasatiempos_becado = '$pasatiempos_becado',
										   relacion_familiar = '$relacion_familiar',
										   porque_necesitas_beca = '$porque_necesitas_beca'
					WHERE id = $id_expediente");


		$url_acta_nacimiento = $this->uploadFile($acta_nacimiento, "assets/expedientes/$id_expediente/", "ACTA DE NACIMIENTO");
		$url_ultima_boleta = $this->uploadFile($ultima_boleta, "assets/expedientes/$id_expediente/", "ULTIMA BOLETA PREPARATORIA");
		$url_carta_mano = $this->uploadFile($carta_mano, "assets/expedientes/$id_expediente/", "CARTA MANO");
		$url_ensayo = $this->uploadFile($ensayo, "assets/expedientes/$id_expediente/", "ENSAYO");
		$url_foto = $this->uploadFile($foto, "assets/expedientes/$id_expediente/", "FOTO");
		
		$db->query("UPDATE expedientes SET url_acta_nacimiento = '$url_acta_nacimiento',
										   url_boleta_preparatoria = '$url_ultima_boleta',
										   url_ensayo = '$url_ensayo',
										   url_carta = '$url_carta_mano',
										   url_foto = '$url_foto'
					WHERE id = $id_expediente");
		
		if(!empty($db->error)){
			$this->sweetAlert("success", "Error", "Se registró un error al capturar la solicitud: " . $db->error);
		}else{
			$this->sweetAlert("success", "Éxito", "Se ha registrado con éxito la solicitud de beca con folio $id_aspirante");
		}
	}
	
	function esCondicionado($id_becado){
		$return = false;
		
		$query=SGBD::sql("SELECT d.* FROM pagos_becados p JOIN detalles_pagos_becados d ON d.id_pago = p.id WHERE d.id_becado='$id_becado' AND d.status = 0 AND p.status = 0 ORDER BY p.id DESC LIMIT 1");
		$row =$query->fetch_array(MYSQLI_ASSOC);
		
		if($query->num_rows > 0){
			if($row['total_pago'] == 0){
				$return = true;
			}
		}
		
		return $return;
	}
	
	function Logout(){
		session_start();
		
		$db = new Conexion();
		
		$id_user = $_SESSION['id_user'];
		$db->query("DELETE FROM sessions WHERE id_user='$id_user'"); // eliminamos todas las cookies en la db que tengan el usuario logueado
		
		session_destroy(); // destruimos la sessión
		setcookie('sesion', "", time() - 1); // borramos la cookies local
		
		if(isset($id_user)){ // verificamos que no se haya reconstruído la session con la función Cookie(), en caso de que sí, vuelve a mandar a logout para terminarla.
			$this->goWeb("logout");
		}else{
			$this->goWeb("login"); // Ya no hay ninguna sesión, cookie local ni cookie en la db, redirige al inicio.
		}
	}

	function LogoutBecado(){
		$db = new Conexion();
		
		$id_becado = $_SESSION['id_becado'];
		
		session_destroy(); // destruimos la sessión
		
		if(isset($id_becado)){ // verificamos que no se haya reconstruído la session con la función Cookie(), en caso de que sí, vuelve a mandar a logout para terminarla.
			$this->goWebPublica("logout");
		}else{
			$this->goWebPublica("becados"); // Ya no hay ninguna sesión, cookie local ni cookie en la db, redirige al inicio.
		}
	}
	
	function goWeb($web){
		echo "<script type='text/javascript'>window.location='https://fundacionce.org/admin/$web';</script>";
	}
	
	function goWebPublica($web){
		echo "<script type='text/javascript'>window.location='https://fundacionce.org/$web';</script>";
	}

	function updateUser($id_user, $name, $pass, $rol, $url){
		if($this->VerifyLogin() == 1){
				
			$user = $this->getDataId('usuario', $id_user);
			
			$db = new Conexion();
			$sql = $db->query("SELECT count(*) FROM usuarios WHERE id='$id_user'");
			$existe = $db->recorrer($sql);
			$total = $existe["count(*)"]; 
			
			if($total > 0){
				$query = "UPDATE usuarios SET nombre = '$name', id_rol = '$rol' WHERE id='$id_user'";
				$db->query($query);
			
			if(!empty($pass)){
				$passHash = password_hash($pass, PASSWORD_BCRYPT);
				$db->query("UPDATE usuarios SET contrasena='$passHash' WHERE id='$id_user'");
			}
			
			if(!empty($url)){
				$db->query("UPDATE usuarios SET url_image='$url' WHERE id='$id_user'");
			}
			
			$user = html_entity_decode($user);
			$this->AddLog("Actualizó al usuario $user");
			$this->sweetAlert("success", "Éxito", "Se ha editado con éxito al usuario $user");
			//$this->goWeb("users");
			
			}else{
				$this->sweetAlert("error", "Error", "No existe el usuario");
			}
		 }
	}
	
	function deleteUser($id){
		
		$db = new Conexion();
		$fecha_actual=date("Y-m-d H:i:s");
	
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE id='$id' AND status = 0");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
			
		if($total > 0){
			$db->query("UPDATE usuarios SET status = 1 WHERE id = $id");
			$this->sweetAlert("success", "Éxito", "Usuario inactivado con éxito");
			$this->AddLog("Inactivó al usuario $id");
		}else{
			$this->sweetAlert("error", "Error", "Ese usuario no existe, inténtalo de nuevo");
		}
		
		//$this->goWeb("users");
	}
	
	function activateUser($id){
		
		$db = new Conexion();
		$fecha_actual=date("Y-m-d H:i:s");
	
		$sql = $db->query("SELECT count(*) FROM usuarios WHERE id='$id' AND status = 1");
		$existe = $db->recorrer($sql);
		$total = $existe["count(*)"]; 
			
		if($total > 0){
			$db->query("UPDATE usuarios SET status = 0 WHERE id = $id");
			$this->sweetAlert("success", "Éxito", "Usuario activado con éxito");
			$this->AddLog("Activó al usuario $id");
		}else{
			$this->sweetAlert("error", "Error", "Ese usuario no existe, inténtalo de nuevo");
		}
		
		//$this->goWeb("users");
	}
	
	function deleteAvatar($id){	
		$db = new Conexion();
		$db->query("UPDATE usuarios SET url_image = 'https://fundacionce.org/admin/assets/images//users/no-avatar.png' WHERE id = $id");
		$this->AddLog("Eliminó el avatar del usuario $id");
		$this->goWeb("usuarios?id=$id");
	}
	
	function deleteAvatarBecado($id){	
		$db = new Conexion();
		$db->query("UPDATE becados SET url_avatar = 'https://fundacionce.org/admin/assets/images//users/no-avatar.png' WHERE id = $id");
		$this->AddLog("Eliminó el avatar del becado $id");
		$this->goWeb("becados?id=$id");
	}
	
	function getData($data, $user){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM usuarios WHERE usuario='$user'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getDataId($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM usuarios WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getRol($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM roles WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}

	function getConfi($data){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM confi WHERE id=1");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getPreparatoria($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM preparatorias WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}

	function getProyecto($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM proyectos WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getUniversidad($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM universidades WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getCarrera($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM carreras WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getGeneracion($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM generaciones WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getCicloEscolar($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM ciclos_escolares WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getCalendario($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM calendario_eventos WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}
	
	function getConferencia($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM conferencias WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getPrograma($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM programas WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getEvento($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM eventos WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getCasoExito($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM casos_exito WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getOtrasBecas($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM otras_becas WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getExpediente($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM expedientes WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getExpedienteArray($id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT * FROM expedientes WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row;
	}	
	
	function getBecado($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM becados WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	

	function getBecadoUuid($data, $uuid){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM becados WHERE uuid='$uuid'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getBecadoArray($id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT * FROM becados WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row;
	}	

	function getAspirante($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM aspirantes WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	

	function getCiudad($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM ciudades WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getAspiranteByExpediente($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM aspirantes WHERE id_expediente='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		if(isset($row)){
			return $row[$data];
		}else{
			return "NULL";
		}
	}	

	function getAspiranteByExpedienteBecado($id_expediente){
		global $db;

		$nombres = $this->getExpediente('nombres', $id_expediente);
		$apellidos = $this->getExpediente('apellidos', $id_expediente);

		$query=SGBD::sql("SELECT a.id as id_aspirante 
							 FROM aspirantes a 
							 JOIN expedientes e ON e.id = a.id_expediente 
							 					AND e.nombres = '$nombres' 
												AND e.apellidos  = '$apellidos'
						ORDER BY a.id_expediente DESC");

		$row =$query->fetch_array(MYSQLI_ASSOC);

		return $row['id_aspirante'];
	}
	
	function getCalificacion($id_ciclo_escolar, $id_becado){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT IFNULL( promedio, 0) as promedio FROM calificaciones_becados WHERE id_ciclo_escolar='$id_ciclo_escolar' AND id_becado = '$id_becado'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		
		$promedio = 0;
		
		if(isset($row)){
			if( !is_null( $row['promedio'])){
				$promedio = $row['promedio'];
			}
		}
		
		return $promedio;
	}	
	
	function getCalificacionAnterior($id_ciclo_escolar, $id_becado){
		$db = new Conexion();

		$id_ciclo_escolar = $this->getCicloEscolar('id_ciclo_anterior', $id_ciclo_escolar);

		$Consulta=SGBD::sql("SELECT IFNULL( promedio, 0) as promedio FROM calificaciones_becados WHERE id_ciclo_escolar='$id_ciclo_escolar' AND id_becado = '$id_becado'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		
		$promedio = 0;
		
		if(isset($row)){
			if( !is_null( $row['promedio'])){
				$promedio = $row['promedio'];
			}
		}
		
		return $promedio;
	}	
	
	function getCampo($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM campos_estudio WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getTipoFamilia($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM tipos_familia WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getPago($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM pagos_becados WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getSolicitudServicioBecado($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM solicitudes_servicio_comunitario WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	

	function getBecadoIngles($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM becados_ingles WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	
	
	function getAsistenciaPsicologica($data, $id){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT $data FROM becados_asistencia_psicologica WHERE id='$id'");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row[$data];
	}	

	function getTotalPago($id_ciclo_escolar, $id_becado){
		$db = new Conexion();
		
		// revisar algoritmo
		/*if($this->getCalificacion($id_ciclo_escolar, $id_becado) < 80){
			return 0;
		}else{
			return $this->getBecado('total_pagos_beca', $id_becado);
		}*/
		
		return $this->getBecado('total_pagos_beca', $id_becado);
	}
	
	function getPagosBecado($id_becado){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT IFNULL( SUM(dp.total_pago), 0) as total FROM detalles_pagos_becados dp JOIN pagos_becados p ON p.id = dp.id_pago WHERE dp.id_becado='$id_becado' AND dp.status = 0 AND p.status = 0");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row['total'];
	}
	
	function getCobranzaBecado($id_becado){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT IFNULL( SUM(total_pago), 0) as total FROM cobranza_becados WHERE id_becado='$id_becado' AND status = 0");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row['total'];
	}
	
	function getHorasServicioBecado($id_becado){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT IFNULL( SUM(horas_liberadas), 0) as total FROM solicitudes_servicio_comunitario WHERE id_becado = '$id_becado' AND estado_actual = 'Liberada' AND status = 0");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row['total'];
	}
	
	function getIdSolicitudServicioBecado($id_becado){
		$db = new Conexion();
		$Consulta=SGBD::sql("SELECT id FROM solicitudes_servicio_comunitario WHERE id_becado = '$id_becado' AND estado_actual <> 'Liberada' AND status = 0");
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return $row['id'];
	}
	
	function getTotalDevolucion($id_becado){
		$db = new Conexion();
		
		$estado_actual = $this->getBecado('estado_actual', $id_becado);
		
		if($estado_actual == 'Baja'){
			$total_devolucion = $this->getPagosBecado($id_becado);
		}else if($estado_actual == 'Graduado'){
			$total_devolucion = $this->getBecado('total_devolucion_programada', $id_becado);
		}else{
			$total_devolucion = $this->getBecado('total_devolucion', $id_becado);
		}
		
		return $total_devolucion;
	}
	
	function getSaldoBecado($id_becado){
		$db = new Conexion();
		
		return $this->getTotalDevolucion($id_becado) - $this->getCobranzaBecado($id_becado);
	}

	function getBecadosFechaReunionDocumentos($fecha, $id_becado){
		$q = "SELECT COUNT(*) as total 
				FROM becados b 
				WHERE b.fecha_reunion_documentos = '$fecha'
				AND b.id <> $id_becado
				AND   b.status = 0";
		$query=SGBD::sql($q);
		$row =$query->fetch_array(MYSQLI_ASSOC);
		return $row['total'];
	}
	
	function getMaquilaBecado($id_becado){
		global $db;
	
		$id_expediente = $this->getBecado('id_expediente', $id_becado);
		$maquila = $this->getExpediente('maquila', $id_expediente);
	
		$quien = "";
	
		if($maquila == 1){
			$quien = "Becado";
		}

		$query = SGBD::sql("SELECT GROUP_CONCAT(parentezco) AS todos_parentescos
							FROM familiares_expedientes
							WHERE id_expediente = '$id_expediente' 
							AND status = 0
							AND maquila = 1");
		$row = $query->fetch_array(MYSQLI_ASSOC);

		if(isset($row['todos_parentescos'])){
			if(!empty($quien)){
				$quien .= ", " . $row['todos_parentescos'];
			}else{
				$quien = $row['todos_parentescos'];
			}
		}
	
		return $quien;
	}

	function getEstadistica($tipo, $id_campo_estudio = 0){
		$db = new Conexion();
		
		$id_user = $_SESSION['id_user'];
		
		$id_generacion = $this->getDataId('filtro_generacion', $id_user);
		$id_universidad = $this->getDataId('filtro_universidad', $id_user);
		
		if($id_campo_estudio == 0){
			$id_campo_estudio = $this->getDataId('filtro_campo_estudio', $id_user);
		}
		
		$id_carrera = $this->getDataId('filtro_carrera', $id_user);
		$id_ciclo_escolar = $this->getDataId('filtro_ciclo_escolar', $id_user);
		$estado_actual = $this->getDataId('filtro_estado_actual', $id_user);
		
		switch($tipo){
			case "total_becados_filtro":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = '$estado_actual' 
					  AND   b.status = 0";
			break;

			case "total_becados":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.status = 0";
			break;
			
			case "total_cursando":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = 'Cursando' 
					  AND   b.status = 0";
			break;
			
			case "total_graduados":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = 'Graduado' 
					  AND   b.status = 0";
			break;
			
			case "total_bajas":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = 'Baja' 
					  AND   b.status = 0";
			break;
			
			case "total_bajas_temporales":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = 'Baja Temporal' 
					  AND   b.status = 0";
			break;
			
			case "promedio_aritmetico":
				$q = "SELECT IFNULL( AVG(promedio), 0) as total 
					  FROM calificaciones_becados cb
					  JOIN becados b ON cb.id_becado = b.id
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   cb.id_ciclo_escolar LIKE '$id_ciclo_escolar'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   b.status = 0";

			break;
			
			case "hijo_mayor":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   e.lugar_familia     = 'Mayor'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   b.status = 0";
			break;
			
			case "hijo_menor":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.lugar_familia     = 'Menor'
					  AND   b.status = 0";
			break;
			
			case "primero_universidad":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.primero_en_universidad     = 1
					  AND   b.status = 0";
			break;
			
			case "otras_becas":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.otras_becas       <> 'NULL'
					  AND   e.otras_becas       IS NOT NULL
					  AND   b.status = 0";
			break;
			
			case "beca_fundacion_chihuahua":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.otras_becas       LIKE '1'
					  AND   b.status = 0";
			break;
			
			case "es_maquila":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.maquila     = 1
					  AND   b.status = 0";
			break;
						
			case "ingreso_familiar_promedio":
				$q = "SELECT IFNULL( AVG( e.ingreso_familiar_total), 0) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   b.status = 0";
			break;
			
			case "ingreso_familiar_percapita":
				$q = "SELECT IFNULL( AVG( e.ingreso_familiar_total / e.numero_personas_casa), 0) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   b.status = 0";
			break;
			
				
			case "hombres":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.sexo             = 'M'
					  AND   b.status = 0";
			break;
			
				
			case "mujeres":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   e.sexo             = 'F'
					  AND   b.status = 0";
			break;
			
			case "campo_estudio":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   b.estado_actual     LIKE '$estado_actual'
					  AND   b.status = 0";
			break;
		}
		
		$Consulta=SGBD::sql($q);
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);
		return round( $row['total'], 2);
	}
	
	function getEstadisticaWeb($tipo){
		$db = new Conexion();
	
		switch($tipo){
			case "total_becados":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.status = 0";
			break;
			
			case "total_cursando":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  AND   b.estado_actual     = 'Cursando' 
					  AND   b.status = 0";
			break;
			
			case "total_graduados":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  AND   b.estado_actual     = 'Graduado' 
					  AND   YEAR(b.fecha_graduado) = YEAR( DATE_ADD(curdate(), INTERVAL -1 YEAR))
					  AND   b.status = 0";
			break;
			
			case "total_generaciones":
				$q = "SELECT COUNT(*) as total 
					  FROM generaciones g
					  WHERE g.id NOT IN(9, 10)
					  AND g.status = 0";
			break;
			
			case "total_universidades":
				$q = "SELECT COUNT(*) as total 
					  FROM universidades u
					  WHERE u.status = 0";
			break;
			
			
			case "total_bajas":
				$q = "SELECT COUNT(*) as total 
					  FROM becados b 
					  JOIN expedientes e ON e.id         = b.id_expediente 
					  JOIN carreras c    ON e.id_carrera = c.id
					  WHERE b.id_generacion     LIKE '$id_generacion'
					  AND   e.id_universidad    LIKE '$id_universidad'
					  AND   c.id_campo_estudio  LIKE '$id_campo_estudio'
					  AND   c.id                LIKE '$id_carrera'
					  AND   b.estado_actual     = 'Baja' 
					  AND   b.status = 0";
			break;
			
		}
		
		$Consulta=SGBD::sql($q);
		$row =$Consulta->fetch_array(MYSQLI_ASSOC);

		return round( $row['total'], 2);
	}
	
	function time_passed($timestamp){
		$diff = time() - (int)$timestamp;

		if($diff < 20)                                     { $return = 'ahora mismo';                                            }
		else if($diff >= 20 AND $diff < 60)             { $return = sprintf('hace %s segundos.'    , $diff);                    }
		else if($diff >= 60 AND $diff < 120)            { $return = sprintf('hace %s minuto.'    , floor($diff/60));            }
		else if($diff >= 120 AND $diff < 3600)            { $return = sprintf('hace %s minutos.'    , floor($diff/60));            }
		else if($diff >= 3600 AND $diff < 7200)            { $return = sprintf('hace %s hora.'        , floor($diff/3600));        }
		else if($diff >= 7200 AND $diff < 86400)        { $return = sprintf('hace %s horas.'    , floor($diff/3600));        }
		else if($diff >= 86400 AND $diff < 172800)        { $return = sprintf('hace %s dia.'        , floor($diff/86400));        }
		else if($diff >= 172800 AND $diff < 604800)        { $return = sprintf('hace %s dias.'        , floor($diff/86400));        }
		else if($diff >= 604800 AND $diff < 1209600)    { $return = sprintf('hace %s semana.'    , floor($diff/604800));        }
		else if($diff >= 1209600 AND $diff < 2629744)    { $return = sprintf('hace %s semanas.'    , floor($diff/604800));        }
		else if($diff >= 2629744 AND $diff < 5259488)    { $return = sprintf('hace %s mes.'        , floor($diff/2629744));    }
		else if($diff >= 5259488 AND $diff < 31556926)    { $return = sprintf('hace %s meses.'    , floor($diff/2629744));    }
		else if($diff >= 31556926 AND $diff < 63113852)    { $return = sprintf('hace %s año.'        , floor($diff/31556926));    }
		else if($diff >= 63113852)                        { $return = sprintf('hace %s años.'        , floor($diff/31556926));    }
		else                                             { $return = date('Y-m-d H:i:s', $timestamp);                        }

		return $return;
	}
	
	function convertTimeZone($timezone, $date){
		$triggerOn = $date;
		$default_tz = 'Europe/Madrid';

		$schedule_date = new DateTime($triggerOn, new DateTimeZone($default_tz));
		$schedule_date->setTimeZone(new DateTimeZone($timezone));
		$triggerOn =  $schedule_date->format('H:i');

		return $triggerOn;
	}
	
	function get_total_days($start, $end, $holidays = [], $weekends = ['Sat', 'Sun']){
		$start = new \DateTime($start);
		$end   = new \DateTime($end);
		$end->modify('+1 day');

		$total_days = $end->diff($start)->days;
		$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

		foreach($period as $dt) {
			if (in_array($dt->format('D'),  $weekends) || in_array($dt->format('Y-m-d'), $holidays)){
				$total_days--;
			}
		}
		return $total_days;
	}
	
	function urls_amigables($url) {
		  // Tranformamos todo a minusculas
	 
		  $url = strtolower($url);
	 
		  //Rememplazamos caracteres especiales latinos
	 
		  $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	 
		  $repl = array('a', 'e', 'i', 'o', 'u', 'n');
	 
		  $url = str_replace ($find, $repl, $url);
	 
		  // Añadimos los guiones
	 
		  $find = array(' ', '&', '\r\n', '\n', '+');
		  $url = str_replace ($find, '-', $url);
	 
		  // Eliminamos y Reemplazamos otros carácteres especiales
	 
		  $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	 
		  $repl = array('', '-', '');
	 
		  $url = preg_replace ($find, $repl, $url);
	 
		  return $url;
	 
	}
	
	function number_f($number){
		return number_format((float)$number, 2, '.', ',');
	}

	function uploadFile($file, $folder_upload, $file_name_upload = ''){
		
		$url = "";
		
		$root = $_SERVER["DOCUMENT_ROOT"];
		
		if (isset($file)) {
			if(!$file['name']==''){
				
				$filepath = $file['tmp_name'];
				$fileSize = filesize($filepath);
				$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
				$filetype = finfo_file($fileinfo, $filepath);
				
				$allowedTypes = [
				   'application/pdf' => 'pdf',
				   'image/png' => 'png',
				   'image/jpeg' => 'jpg',
				   'image/jpeg' => 'gif',
				   'image/jpeg' => 'jpeg'
				];
				
				if ($fileSize === 0) {
					$errors = "Seleccione un archivo válido";
				}else if ($fileSize > 52428800) {
					$errors = "Seleccione un archivo no mayor a 50 MB";
				}else if(!in_array($filetype, array_keys($allowedTypes))) {
					$errors = "Seleccione un archivo de tipo PNG o JPG";
				}else{
					//basename($filepath)
					
					
					if(empty($file_name_upload)){
						$filename = "archivo_" . time();
					}else{
						$filename = $file_name_upload;
					}
					
					if (!file_exists($root . "/" . $folder_upload)) {
						mkdir($root . "/" . $folder_upload, 0777, true);
					}

					$extension = $allowedTypes[$filetype];
					$directory = $folder_upload;
					$targetDirectory = $root . "/" . $directory; //__DIR__ . "/" . $directory; 

					$newFilepath = $targetDirectory . $filename . "." . $extension;

					if (!copy($filepath, $newFilepath )) {
						$errors = "No se pudo subir el archivo con éxito, inténtalo de nuevo.";
					}else{
						$url = 'https://fundacionce.org/' . $directory . $filename . "." . $extension;
					}
				
					unlink($filepath); 
				}
			}
		}
		
		if(!empty($errors)){
			echo $errors;
		}else{
			return $url;	
		}
	}

	function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );

		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );

		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );

		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}

	function SpanishDate($FechaStamp)
		{
		$ano = date('Y',$FechaStamp);
		$mes = date('n',$FechaStamp);
		$dia = date('d',$FechaStamp);
		$diasemana = date('w',$FechaStamp);
		$diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
						"Jueves","Viernes","Sábado");
		$mesesN=array(1=>"ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO",
					"AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		return $mesesN[$mes] ." $ano";
		}  

	function sendNotification($id_becado, $texto) {
		global $db, $fecha_actual;

		$tokenFirebase = $this->getBecado('token_firebase', $id_becado);

		$db->query("INSERT INTO notificaciones(id_becado, texto, fecha) VALUES('$id_becado', '$texto', '$fecha_actual')");

		// FCM API Url
		$url = 'https://fcm.googleapis.com/fcm/send';
	
		// Put your Server Key here
		$apiKey = "AAAAnITwB2w:APA91bHLKVt0_sBoFe5I0X5LrM2pOQDghLSKFhOsICesAi3d6HPpcsO1o2jhXfz3uAP0wo4SJgH1yo_k9s_KoNAmAwXQnD9keWKjYHKtVGyvJVCyRwbr7shvXfKxYfv8GOHshd8Po3fn";
	
		// Compile headers in one variable
		$headers = array (
			'Authorization:key=' . $apiKey,
			'Content-Type:application/json'
		);

		$dataPayload = [
			//'id_notification' => $id_notificacion,
			'title' => 'Becados CE',
			'body'=> "$texto",
			//'url_action' => $url_action
		];
	
		$apiBody = [
			'notification' => $dataPayload,
			'to' => $tokenFirebase
		];
	
		// Initialize curl with the prepared headers and body
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($apiBody));
		//curl_setopt ($ch, CURLOPT_POSTFIELDS, $apiBody);
	
		// Execute call and save result
		$result = curl_exec($ch);
		
		//$db->query("INSERT INTO logs_firebase(id_notificacion, text, token_firebase, text_firebase, fecha_registro) VALUES('$id_notificacion', '$text', '$tokenFirebase', '$result', '$fecha_actual')");
		//print($result);
		// Close curl after call
		curl_close($ch);

		echo $result;
	
		return $result;
	}
}
?>
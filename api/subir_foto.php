
<?
	include("../confi/core.php");

	$jsonresponse = array();

    if (isset($_FILES["file"])) {
        if(!$_FILES['file']['name']==''){
            $filepath = $_FILES['file']['tmp_name'];
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);
            
            $allowedTypes = [
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/jpeg' => 'gif',
                'image/jpeg' => 'jpeg'
            ];
            
            if ($fileSize === 0) {
                $errors = "Seleccione un archivo válido";
            }else if ($fileSize > 52428800) { // 3 MB (1 byte * 1024 * 1024 * 3 (for 3 MB))
                $errors = "Seleccione un archivo no mayor a 50 MB";
            }else if(!in_array($filetype, array_keys($allowedTypes))) {
                $errors = "Seleccione un archivo de tipo PNG o JPG";
            }else{
                //basename($filepath)
                
                $filename = "foto_" . time(); // I'm using the original name here, but you can also change the name of the file here
                $extension = $allowedTypes[$filetype];
                $directory = "fotos/";
                $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . "/clientes/adogme/api/" . $directory; // __DIR__ is the directory of the current PHP file

                $newFilepath = $targetDirectory . $filename . "." . $extension;

                if (!copy($filepath, $newFilepath )) { // Copy the file, returns false if failed
                    $errors = "No se pudo subir el archivo con éxito, inténtalo de nuevo.";
                }else{
                    $url = 'https://aslsoft.dev/clientes/adogme/api/' . $directory . $filename . "." . $extension;
                }
               
                if(!empty($errors)){
                    $jsonresponse = array( 'status' => false, 'message' => $errors);
                }else{
                    $jsonresponse = array( 'status' => true, 'message' => 'Se ha subido con éxito la foto', 'url_foto' => $url);
                }
                
                unlink($filepath); // Delete the temp file
            }
        
        }else{
            $jsonresponse = array( 'status' => false, 'message' => 'Suba un archivo válido.');
        }
    }else{
        $jsonresponse = array( 'status' => false, 'message' => 'Suba un archivo válido.');
    }
        
	echo json_encode($jsonresponse);

?>
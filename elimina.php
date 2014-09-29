<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("./postgres.php");

	if(isset($_POST['id_pag']) && !empty($_POST['id_pag'])){	
		include_once("./postgres.php");
		$db = new postgres();				
		$borrar = $db->eliminar('paginas', 'id=\''.$_POST['id_pag'].'\'');
		if($borrar)
			echo 'Se borro satisfactoriamente';
		else
			echo 'Hubo un error al borrar la pagina. Inténtalo nuevamente.';
	}
?>
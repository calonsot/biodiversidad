<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("./postgres.php");

if (isset($_POST['data']) && !empty($_POST['data']) && isset($_POST['id']) && !empty($_POST['id']) )
{

	$db = new postgres();
	$data = json_decode($_POST['data']);	

	$guardo = $db->update('paginas', array('json' => $_POST['data'], 'ya_clasificado' => (int)$_POST['ya_clasificado']), 'id='.$_POST['id']);
	
	if($guardo)
		echo 'Los cambiso se guardaron satisfactoriamente';
	else
		echo 'Hubo un error al guardar los datos. Inténtalo nuevamente.';
}
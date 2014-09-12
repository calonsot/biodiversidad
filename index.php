<style>
	table {width: 1000px;}
	td {text-align: center;}
	.delete{width:30px;height:30px;}
	.delete:HOVER{cursor: pointer;}
	.row:HOVER{background:#EFF;}
</style>
<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("./postgres.php");

$db = new postgres();

$paginas = $db->select('paginas', '*', NULL, 'ya_clasificado, pagina');

?>
<script src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
function borra(id){
	document.getElementById("row_"+id).style.display="none";
}

delete_json = function (id)
{
	if(confirm("¿Esta seguro de eliminar la página?")){
		document.getElementById("row_"+id).style.display="none";
		
		$.ajax({
	        url: 'elimina.php',
	        type: 'POST',
	        success: function (msj) {
	            alert(msj);
	        },
	        data: { id_pag: id}
	    });	
	    return false;
	}    
}
</script>
<form name="principal" action="" method="POST">
<table id="tabla_01">
	<thead>
		<tr>
			<th>P&aacute;gina 2</th>
			<th>Contenido</th>
			<th>¿Ya clasificado?</th>
		</tr>
	</thead>
	<tbody>
	
	<?php
	foreach ($paginas as $k => $field)
	{		
		echo '<tr class="row" id="row_'.$field->id.'">';		
		echo "<td><a href='".$field->pagina."' target='_blank'>".$field->pagina."</a></td>";
		echo '<td>';
		echo "<a href='procesa.php?page=".$field->pagina."&format=view&id=".$field->id."'>Texto plano</a>";
		echo ' | ';
		echo "<a href='procesa.php?page=".$field->pagina."&format=edit&id=".$field->id."'>Modificar</a>";
		echo '</td>';		
		echo '<td>'.($field->ya_clasificado ? '1' : '0').'</td>';		
		echo '<td><img class="delete" id='.$field->id.' src="img/delete.png" onclick="delete_json(id)"/></td>';
		echo '</tr>';
	}
?>
</tbody>
</table>
</form>
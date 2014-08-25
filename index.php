<style>
	table {width: 1000px;}
	td {text-align: center;}
</style>
<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("./postgres.php");

$db = new postgres();

$paginas = $db->select('paginas', '*', NULL, 'ya_clasificado, pagina');
?>

<table>
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
		echo '<tr>';
		echo "<td><a href='".$field->pagina."' target='_blank'>".$field->pagina."</a></td>";
		echo '<td>';
		echo "<a href='procesa.php?page=".$field->pagina."&format=view&id=".$field->id."'>Texto plano</a>";
		echo ' | ';
		echo "<a href='procesa.php?page=".$field->pagina."&format=edit&id=".$field->id."'>Modificar</a>";
		echo '</td>';
		echo '<td>'.($field->ya_clasificado ? '1' : '0').'</td>';
		echo '</tr>';
	}
?>

</tbody>
</table>
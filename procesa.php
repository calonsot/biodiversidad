<?php

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("arc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Modifica etiquetas</title>
<style>
	.delete{width:30px;height:30px;}
	.delete:HOVER{cursor: pointer;}
</style>
<script src="js/jquery-1.9.1.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript">

borra_tag= function (obj)
{	
	$('#'+obj+'div').remove();
}
to_json = function ()
{
	var data = new Object();
	var fieldValuePairs = $('form').serializeArray();

	$.each(fieldValuePairs, function(index, fieldValuePair) 
	{
		
		var name = fieldValuePair.name.split('-|-');
		//Exclusivamente para valores
		if (name.length == 4)
		{
			if (data[name[1]] == undefined){
				data[name[1]] = new Array();                     //crea el array para multiples etiquetas				
			}else
				console.log(data[name[1]]);
			
			if (data[name[1]][parseInt(name[2])] == undefined)   //asigna el objeto para e mapeo de los atributos => valores
				data[name[1]][parseInt(name[2])] = new Object();               
			data[name[1]][parseInt(name[2])][name[3]] = fieldValuePair.value.replace(/'/g, "\"");   // asigna el atributo, valor
		}
	});
	return data;	
}

save_json = function ()
{
	var data = to_json();

	$.ajax({
        url: 'guarda_json.php',
        type: 'post',
        success: function (msj) {
            alert(msj);
        },
        data: { data: JSON.stringify(data), 
            id: "<?php echo $_GET['id']; ?>",
            ya_clasificado: $('#ya_clasificado').val() }
    });    
}
</script>

<style>
td {
	vertical-align: top;
}
</style>
</head>
<body>

	<?php
	if (isset($_GET['page']) && !empty($_GET['page']) && isset($_GET['id']) && !empty($_GET['id']))
	{
		$arc = new Arc($_GET['page']);
		$content = $arc->get_content();

		if (isset($_GET['format']) && $_GET['format'] == 'edit')
		{
			echo '<button id="boton" onclick="save_json();" style="position:absolute;margin-left:400px;cursor:pointer;">Guardar</button>';
			echo "<form name='edit' action='edit.php' method='post'>";			
			echo "<b>¿Ya esta clasificado este contenido?</b>: <select name='ya_clasificado' id='ya_clasificado'>";			
			echo "<option value='0' selected>No</option>";
			echo "<option value='1'>S&iacute;</option>";
			echo '</select>';
			/*foreach ($content as $tag => $data){
				foreach ($data as $k => $attributes){
					foreach ($attributes as $attribute => $value){
						echo "attributes: ".$attribute."<br>";
					}					
				}
			}*/
			foreach ($content as $tag => $data)
			{
				if ($tag == 'plaintext')  //hace mas grande el textarea del plaintext
				{
					$rows = '75';
					$cols = '100';
					$flag = true;	
				} else {
					$rows = '5';
					$cols = '50';
					$flag = false;
				}				
				
				echo '<h3>'.$tag.'</h3>';				
				echo '<ol>';				
				foreach ($data as $k => $attributes)
				{					
					$valor = '';
					$html_tag = '<'.$tag.' ';  //para desplegar la etiqueta en el browser																		
					echo '<table><tr><td>';
					echo "<div id='".$tag."_".$k."div'>";					
					echo '<table>';
					echo '<li>';
					echo "<a onclick='borra_tag(\"".$tag."_".$k."\");'><img class='delete' src='img/delete.png'/></a>";
					foreach ($attributes as $attribute => $value)
					{
						$attribute == 'href uri' ? $tag == 'a' ? $valor = $value : '' : '';
						if($attribute == 'cdata')
							$link = $value;
						else
							$link = '';
						$html_tag.= $attribute."='".$value."' ";
														
						echo '<tr>';												
						echo '<td>';						
						echo "<label for='attr-|-".$tag.'-|-'.$k."'>Atributo:</label>";
						echo '</td>';
						echo '<td>';
						echo "<input type='text' size='50' value='".$attribute."' name='attr-|-".$tag.'-|-'.$k."'><br>";
						echo '</td>';
						echo '</tr>';
						echo '<tr>';
						echo '<td>';
						echo "<label for='val-|-".$tag.'-|-'.$k.'-|-'.$attribute."'>Valor:</label>";
						echo '</td>';												
						echo '<td>';						
						echo "<textarea style='float:left;' rows='".$rows."' cols='".$cols."' id='val-|-".$tag.'-|-'.$k.'-|-'.$attribute."' name='val-|-".$tag.'-|-'.$k.'-|-'.$attribute."'>".$value."</textarea><br>";
						if($flag){
							echo "<table>";
							echo "<tr>";
							echo "<td class='plaintext'>";
							$command = './sh/plaintext.sh sh/salida.txt';
							echo exec($command);
							$command = 'rm -f sh/salida.txt';
							exec($command);
							echo "</td>";
							echo "</tr>";
							echo "</table>";
						}
						echo '</td>';
						echo '</tr>';
					}
					echo '</td>';
					echo $tag == 'img' ? $html_tag.= ' style="float:right;">' : '';   //solo para imagenes
					echo $tag == 'embed' ? $html_tag.= ' style="float:right;">' : '';   //solo para swf
					echo $tag == 'a' ? "<a href='".$valor."' target='_blank' style='float:right;'>".$link."</a>" : ''; //solo para links 
					echo '</li></table>';
					echo '</div>';
					echo '</table>';
				}
				echo '</ol>';
			}
			echo '</form>';
			echo '<button id="boton" onclick="save_json();" style="cursor:pointer;">Guardar</button>';
		}
		if (isset($_GET['format']) && $_GET['format'] == 'view')
		{
			echo '<pre>';
				print_r($content);	
			echo '</pre>';
		}
	}
	?>	
</body>
</html>
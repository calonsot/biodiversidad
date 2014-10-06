<?php
/**
 * Carlos R. Alonso Torres
 * Migracion de biodiversidad a una base de datos, ocupando ARC2 09/08/2014
 */
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../arc2/ARC2.php");
include_once("./simple_html_dom.php");
include_once("./html2text.php");
include_once("./postgres.php");


class Arc
{
	private $page;
	private $page_obj;
	private $db;

	public function __construct ($page)
	{
		$this->page = $page;
	}

	private function arc ()
	{
		$config = array('auto_extract' => 0);
		$parser = ARC2::getSemHTMLParser($config);
		$parser->parse($this->page);
		//return $parser;
		return $this->set_important_fields($parser);
	}

	private function set_important_fields ($parser)
	{
		$nodes = $this->nodes($parser);
		return $nodes;
	}

	private function headers($parser)
	{
		return array
		(
				'page' => $parser->base,
				'target_encoding' => $parser->target_encoding,
				'xml' => $parser->xml,
				'rdf' => $parser->rdf
		);
	}
	
	private function nodes ($parser)
	{
		$nodes = array();		
		$prefix = 'http://www.biodiversidad.gob.mx/';
		$sufix = '.html';
		
		$link_disable = $this->links_no_validos();
		$patterns = $this->patrones();		
		foreach ($parser->nodes as $key => $data)
		{
			switch ($k = $data['tag_exact'])
			{
				/*case 'doctype':
					$attrs = $this->set_tag_attributes($data, $k);
				if (!empty($attrs))
				{
				if (!isset($nodes[$k]))
					$nodes[$k] = array();
				array_push($nodes[$k], $attrs);
				}
				//$nodes[$k]['html'] = convert_html_to_text('<!'.strtoupper($k).' sHTML PUBLIC '.str_replace('"', "\"", $nodes[$k][0]['value']));
				break;*/
				case 'title':
					$nodes[$k] = array(array('value' => $data['cdata']));
					break;
				case 'meta':
					$attrs = $this->set_tag_attributes($data, $k);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
						$nodes['plaintext'] = array(array('value'=>$this->get_dom_plaintext()));
					}					
					break;

				/*case 'link':
					$attrs = $this->set_tag_attributes($data, $k, NULL, '1');
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
																		
						array_push($nodes[$k], $attrs);
					}
					break;*/
				/*case 'style':
					$attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
					}
					break;*/
				/*case 'script':   //falta fragmentar el javascript dentro de la pagina
					$attrs = isset($data['a']['src uri']) ? $this->set_tag_attributes($data, $k) : $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
					}
					break;*/
					/*case 'body':
					 $attrs = $this->set_tag_attributes($data, $k);
					if (!empty($attrs))
					{
					if (!isset($nodes[$k]))
						$nodes[$k] = array();
					array_push($nodes[$k], $attrs);
					}
					break;*/
				case 'a':
					$flag = true;
					$attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						
						foreach($link_disable as $word){
							$href_uri = $attrs['href uri'];
							if($href_uri==$prefix.$word."/".$word.$sufix || $href_uri==$prefix."menusup/".$word.$sufix ||
									$href_uri==$prefix.$word.$sufix)
								$flag=false;
						}
						
						if($flag)
							array_push($nodes[$k], $attrs);												
					}
					break;
					/*case 'span':
					 $attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
					if (!isset($nodes[$k]))
						$nodes[$k] = array();
					array_push($nodes[$k], $attrs);
					}
					break;*/
				case 'img':
					$flag = true;
					
					$attrs = $this->set_tag_attributes($data, $k, NULL, '1');
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();						
						$attrs['src'] = $attrs['src uri'];
						foreach ($patterns as $pattern){
							if(preg_match($pattern, $attrs['src uri']))
								$flag=false;
						}												
						
						unset($attrs['src uri']);
						unset($attrs['id uri']);
						if($flag)
							array_push($nodes[$k], $attrs);
					}
					break;
				case 'object':
					$attrs = $this->set_tag_attributes($data, $k, NULL);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();						
						unset($attrs['id uri']);
						array_push($nodes[$k], $attrs);
					}
					break;
				case 'embed':
					$attrs = $this->set_tag_attributes($data, $k, NULL);					
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						$attrs['src'] = $attrs['src uri'];
						unset($attrs['src uri']);
						unset($attrs['id uri']);
						array_push($nodes[$k], $attrs);
					}
					break;
					/*case 'div':
					 $attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
					if (!isset($nodes[$k]))
						$nodes[$k] = array();
					array_push($nodes[$k], $attrs);
					}
					break;*/
					/*case 'p':
					 $attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
					if (!isset($nodes[$k]))
						$nodes[$k] = array();
					array_push($nodes[$k], $attrs);
					}
					break;*/
					/*case 'cdata':
					 $attrs = $this->set_tag_attributes($data, $k);
					$cdata = trim($data['a']['value']);
					if (!empty($attrs) && !empty($cdata))
					{
					if (!isset($nodes[$k]))
						$nodes[$k] = array();
					array_push($nodes[$k], $attrs);
					}
					break;*/
			}
		}		
		//$nodes['headers'] = array($this->headers($parser));	
		return $nodes;
	}

	private function set_tag_attributes ($data, $name, $cdata = false, $empty = '0')
	{
		if($data['empty'] == $empty)
		{
			$n = array($name => array());   //single node
			foreach ($data['a'] as $attribute => $value)
			{
				if (!preg_match('/ m$/', $attribute))
					$n[$name][$attribute] = utf8_encode($value);
			}
			if ($cdata)    //por si es necesario mostrar cdata
			{
				$cdat = trim($data['cdata']);
				if (!empty($cdat))
					$n[$name]['cdata'] = utf8_encode($data['cdata']);
				else
					return NULL;
			}
			return $n[$name];

		} else
			return 0;
	}
	
	private function json () 
	{
		$data = json_decode($this->page_obj->json);
		return $data;	
	}

	private function get_dom_plaintext ()
	{
		$html = file_get_html($this->page);
		$command = './sh/formato.sh \''.$html.'\' sh/pagina.html';
		$salida=exec($command);
		$command = './sh/parameters.sh \'Inicio\' \'bioc1_19.png\' sh/pagina.html sh/resultado.txt';
		$salida=exec($command);
		$command = './sh/formato_lineal.sh sh/resultado.txt sh/pagina.html';
		$salida=exec($command);		
		$salida = file_get_html("sh/pagina.html");		
		$out = $salida->plaintext;
		$command = './sh/links.sh \''.$out.'\' sh/salida.txt';
		$out = exec($command);
		$command = './sh/limpiar.sh sh/resultado.txt sh/pagina.html sh/salida.txt';
		exec($command);
		return $out;
	}

	private function conexion ()
	{
		$con = new postgres();
		$this->db = $con;
	}

	public function get_content ()
	{
		$this->conexion();
		$pagina = $this->db->select('paginas', '*', "pagina='".$this->page."'");
		$this->page_obj = $pagina[0];		
		return empty($this->page_obj->json) ? $this->arc() : $this->json();
	}
	
	private function patrones(){
		$prefix_img = '/http:\D\Dwww.biodiversidad.gob.mx\D';
		$patron = $prefix_img."biodiversidad\Dimages\Dbioc1_[0-9][0-9].png+$/";		
		$patron2 = $prefix_img."images\Dindex_nw_[0-9][0-9].png+$/";
		$patron3 = $prefix_img."+(spacer.gif)|(icimpresion_21.png)$/";
		$patron4 = $prefix_img."+(logoprueba_[0-9][0-9].png)|(contycred_[0-9][0-9].png)|(menu_prin_[0-9][0-9][a]?.png)$/";
		$patron5 = $prefix_img."+(balazo_\w*.png)|(biodiv_new_[0-9][0-9].png)|(tabla2_[0-9][0-9].png)$/";
		$patron6 = $prefix_img."+(descarga_\w*.png)|(ic\w*.png)|(imgPMNinos_\w*.png)|(logo\w*.png)|(m[1-5]_\w*.png)$/";
		$patron7 = $prefix_img."+(mapsite_\w*.png)|(opIngles\w*.png)|(vv_\w*.png)|(titMexMegadiv_20.png)|(Log20Anios_CONABIO\w*.png)$/";
		$patron8 = $prefix_img."+(DB.png)|(2013logo\w*.png)|(barra_[0-9].png)|(biodiversidad\w*.png)|(corredor\w*.png)$/";
		$patron9 = $prefix_img."+(ecosistemas\w*.png)|(especies\w*.png)|(f_\w*.png)|(genes\w*.png)|(pais\w*.png)|(planeta\w*.png)$/";
		$patron10 = $prefix_img."+(region_\w*.png)|(t_biodiversidad_[0-9].png)|(t_corredor_[0-9].png)|
			(t_ecosistemas_[0-9].png)|(t_pais_[0-9].png)|(t_planeta_[0-9].png)|(t_region_[0-9].png)|(t_usos_[0-9].png)$/";
		return $patterns = array($patron,$patron2,$patron3,$patron4,$patron5,$patron6,$patron7,$patron8,$patron9,$patron10);
	}
	
	private function links_no_validos(){
		$link_disable = array('biodiversidad','ecosistemas','especies','genes',
				'usos','corredor','region','pais','planeta','comentarios',
				'creditos','ninos','recursos','difusion','mapa','index');
	
		return $link_disable;
	}	
}

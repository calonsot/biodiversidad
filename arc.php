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
					}
					break;
				case 'link':
					$attrs = $this->set_tag_attributes($data, $k, NULL, '1');
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
					}
					break;
				case 'style':
					$attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
					}
					break;
				case 'script':   //falta fragmentar el javascript dentro de la pagina
					$attrs = isset($data['a']['src uri']) ? $this->set_tag_attributes($data, $k) : $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
						array_push($nodes[$k], $attrs);
					}
					break;
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
					$attrs = $this->set_tag_attributes($data, $k, true);
					if (!empty($attrs))
					{
						if (!isset($nodes[$k]))
							$nodes[$k] = array();
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
					$attrs = $this->set_tag_attributes($data, $k, NULL, '1');
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
		$nodes['plaintext'] = array(array('value'=>$this->get_dom_plaintext()));
		$nodes['headers'] = array($this->headers($parser));
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
		return $html->plaintext;
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
}

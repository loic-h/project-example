<?php

namespace loiic;
use function \Processwire\wire;

class Helper {

	private $viewBox = array();
	private $countries = array();
	private $preloads = array();
	private $_jsgobals = array();

	public function __construct() {

	}

	/**
	* return html code for a svg sprite
	*
	* @param string $id is the name of the svg file in src
	* @param boolean $setSizes set if the sizes of the sprite should be set as attributes (prevent the svg's from adaptint to parent)
	* @return string
	*/
	public function sprite($id, $setSizes = true) {
		if (!$this->viewBox) {
			$path = wire('config')->paths->resources . 'sprite.svg';
			$doc = new \DOMDocument();
			$doc->Load($path);

			foreach ($doc->getElementsByTagName('symbol') as $symbol) {
				$this->viewBox[$symbol->getAttribute('id')] = $symbol->getAttribute('viewBox');
			}
		}

		if (isset($this->viewBox[$id])) {
			$viewBox = $this->viewBox[$id];
			$sizes = explode(' ', $viewBox);
			$szesStr = $setSizes ? ' width="'.$sizes[2].'" height="'.$sizes[3].'"' : '';
			$url = wire('config')->urls->resources . 'sprite.svg';
			return <<<EOT
<svg viewBox="$viewBox"$szesStr>
	<use xlink:href="$url#$id"></use>
</svg>
EOT;
		}
	}

	/**
	* return html code for a hover item (bold effect)
	*
	* @param string $label the label to be shown
	* @param boolean $href url of the link
	* @param boolean $isActive should we add an active class
	* @return string
	*/
	public function getHoverItem($label, $href, $isActive = false, $attrs = array()) {
		$attributes = array(
			'class' => array('hover')
		);
		if ($isActive) {
			array_push($attributes['class'], 'active');
		}
		foreach ($attrs as $k => $v) {
			if (!isset($attributes[$k])) {
				$attributes[$k] = array();
			}
			if (is_array($v)) {
				foreach ($v as $kk => $vv) {
					array_push($attributes[$k], $vv);
				}
			} else {
				array_push($attributes[$k], $v);
			}
		}
		$attrStr = array_map(function($as, $id) {
			return $id . '="' . implode($as, ' ') . '"';
		}, $attributes, array_keys($attributes));
		$attrStr = implode($attrStr, ' ');
		return <<<EOT
<a href="$href" title="$label" $attrStr>
	<span>$label</span>
</a>
EOT;
	}

	/**
	* harmonize data for header and footer menus
	*
	* @param array $data array of values with either page (pw selector), label, href, attr
	* @return array
	*/
	public function getMenuItems($data) {
		$items = array();
		foreach ($data as $id => $values) {
			$item = array(
				'label' => '',
				'href' => '#',
				'page' => null,
				'attr' => array(),
				'active' => null
			);
			if (isset($values['page'])) {
				$p = wire('pages')->get($values['page']);
				$item['label'] = $p->title;
				$item['href'] = $p->url;
				$item['page'] = $p;
			}
			if (isset($values['label'])) {
				$item['label'] = $values['label'];
			}
			if (isset($values['href'])) {
				$item['href'] = $values['href'];
			}
			if (isset($values['attr'])) {
				$item['attr'] = $values['attr'];
			}
			if (isset($values['active'])) {
				$item['active'] = $values['active'];
			}
			$items[$id] = $item;
		}
		return $items;
	}

	/**
	* return a json with files to be preloaded in JS
	*
	* @param array $data string or array of string of files path
	* @return array
	*/
	public function preload($data) {
		if (is_array($data)) {
			foreach($data as $d) {
				$this->preload($d);
			}
		} else {
			$array = array(
				'id' => preg_replace('/[\/\.]/i', '-', $data),
				'src' => $data
			);
			if (!in_array($array, $this->preloads)) {
				array_push($this->preloads, $array);
			}
		}
	}

	/**
	* return the preload arry as json
	*
	* @return string
	*/
	public function getPreload() {
		return json_encode($this->preloads);
	}

	/**
	* set and return golbals for JS
	*
	* @param string $key key of the data, separated by a "." if need to go deep
	* @param misc $value
	* @return array
	*/
	public function jsglobals($key = null, $value = null) {
		if (!isset($key)) {
			return $this->_jsgobals;
		}
		if (is_array($key)) {
			foreach($key as $k => $v) {
				$this->jsglobals($k, $v);
			}
			return;
		}
		$r = & $this->_jsgobals;
		$keys = explode('.', $key);
		foreach ($keys as $i => $k) {
			if (!isset($r[$k])) {
				$r[$k] = array();
			}
			$r = & $r[$k];
		}
		$r = $value;
	}

	/**
	* Log dump in a nice way
	*
	* @param string $str String to log
	* @return string
	*/
	public function dump($str) {
		echo '<pre>';
		var_dump($str);
		echo '</pre>';
	}
}

function helper() {
	static $instance = null;
	if (!$instance) {
		$instance = new Helper();
	}
	return $instance;
}

?>

<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_File extends Bamboo_Field
{
	/**
	 * @var  string  path where the file will be saved to/ loaded from
	 */
	public $path;
	
	/**
	 * @var  string  The MIME type of files you want this field to accept
	 */
	public $accept; // TODO: implement!

	public function input($name, array $attr = array())
	{
		$attr['type'] = 'file';
		$r = Form::input($name, '', $attr);
		if ($this->value != '') {
			$r .= '[ ' . HTML::anchor($this->__toString()) . ']';
		}
		return $r;
	}
	
	public function __toString()
	{
		if (isset($this->value) && $this->value != null) {
			return $this->path . $this->value;
		}
		return '';
	}
    
}

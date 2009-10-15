<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_Timestamp extends Bamboo_Field_Integer
{
	public $format = 'Y-m-d';
    
    public function input($name, array $attr = NULL)
    {
    	$attr['class'] = isset($attr['class']) ? $attr['class'] . ' date-picker' : 'date-picker';
        return parent::input($name, $attr);
    }
	
	public function __toString()
	{
		return date($this->format, $this->value);
	}
}

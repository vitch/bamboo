<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_Timestamp extends Bamboo_Field_Integer
{
	public $format = 'Y-m-d';
	
    public function set($value)
    {
    	// TODO: There must be a nicer way??
    	if (((int)$value) . '' != $value) {
    		return parent::set(strtotime($value));
		} else {
			return parent::set($value);
		}
    }
	
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

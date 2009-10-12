<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_Boolean extends Bamboo_Field
{
	public $empty = TRUE;
	public $default = FALSE;
	
    public function set($value)
    {
        return parent::set((bool) $value);
    }
	
	public function input($name, array $attr = NULL)
	{
		return Form::checkbox($name, 1, (bool)$this->value, $attr);
	}
	
	public function __toString()
	{
		return $this->value ? 'Yes' : 'No';
	}
    
}

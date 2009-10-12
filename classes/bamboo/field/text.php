<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_Text extends Bamboo_Field_Character
{
    
	public function input($name, array $attr = NULL)
	{
		return Form::textarea($name, $this->value, $attr);
	}
}

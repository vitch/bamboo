<?php 
defined('SYSPATH') or die('No direct script access.');

abstract class Bamboo_Field
{

	public $primary = FALSE;
	
	public $null = FALSE;
	
	public $editable = TRUE;
	
	public $default = '';
	
	public $label;
	
	protected $value;
    
    public function __construct(array $options = NULL)
    {
        if (! empty($options)) {
            $props = get_object_vars($this);
            
            unset($props['value']);
            
            $options = array_intersect_key($options, $props);
            
            foreach ($options as $key=>$value) {
                $this->$key = $value;
            }
        }
        
        if ($this->default !== NULL) {
            $this->set($this->default);
        }
    }
    
    public function get()
    {
        return $this->value;
    }
	
	public function raw()
	{
		return $this->value;
	}
    
    public function set($value)
    {
        if ($this->null AND empty($value)) {
            // Empty values are converted to NULLs
            $value = NULL;
        }
        
        if ($this->value === $value) {
            return FALSE;
        }
        
        $this->value = $value;
        
        return TRUE;
    }
    
    public function __toString()
    {
        return (string) $this->value;
    }
    
    public function input($name, array $attr = NULL)
    {
        return Form::input($name, $this->__toString(), $attr);
    }
    
    public function label($name, array $attr = NULL)
    {
        return Form::label($name, $this->label, $attr);
    }
    
}

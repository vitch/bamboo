<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_HasOne extends Bamboo_Field_Integer
{
	public $model;
	
	private $relatedModel;
	
    public function get()
    {
    	if (!isset($this->relatedModel)) {
	    	if (isset($this->value)) {
	    		$this->relatedModel = Bamboo::factory($this->model);
				$this->relatedModel->{$this->relatedModel->__id_field} = $this->raw();
				$this->relatedModel->load();
	    	}
		}
        return $this->relatedModel;
    }
	
    public function set($value)
	{
		if (parent::set($value)) {
			unset($this->relatedModel);
		}
	}
	
	public function input($name, array $attr = NULL)
	{
		// TODO: cache?
		$relatedItems = Bamboo::get_list($this->model);
		$options = array();
		foreach($relatedItems as $relatedItem) {
			$options[$relatedItem->{$relatedItem->__id_field}->raw()] = $relatedItem->{$relatedItem->__name_field}->raw();
		}
		return Form::select($name, $options, $this->value, $attr);
	}
	
	public function __toString()
	{
		return $this->get()->{$this->relatedModel->__name_field}->raw();
	}
}

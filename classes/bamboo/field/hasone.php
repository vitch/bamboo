<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_HasOne extends Bamboo_Field_Integer
{
	public $model;
	
	private $relatedModel;
	
    public function get()
    {
    	if (!isset($this->relatedModel)) {
	    	if ($this->value) {
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
}

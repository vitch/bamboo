<?php 
defined('SYSPATH') or die('No direct script access.');

class Bamboo_Field_Integer extends Bamboo_Field
{

    public function set($value)
    {
        return parent::set((int) $value);
    }
    
}

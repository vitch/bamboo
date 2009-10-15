<?php 
defined('SYSPATH') or die('No direct script access.');
/**
 * Bamboo is a scaffolding module for Kohaha. Similar in intent to Sprig but slightly different in implementation...
 *
 * @package    Bamboo
 * @author     Kelvin Luck
 * @copyright  (c) 2009 Kelvin Luck
 * @license    MIT
 */
abstract class Bamboo
{
	
	protected $_db;
	protected $_table;
	protected $_fields;
	protected $_id_field;
	protected $_name_field;
	protected $_sort_on;
	
	public static function factory($name, array $values = array())
	{
		$class = 'Model_'.$name;
		$model = new $class;
		$model->_init();
		$model->values($values);
		return $model;
	}
	
	public static function get_list($name, $deep = false, $where = array(), 
									$order_by = null, $order_by_dir = 'ASC',
									$limit_offset = null, $limit = null)
	{
		$obj = Bamboo::factory($name);
		$query = DB::select()
					->from($obj->_table);
		if ($deep) {
			call_user_func_array(array($query, 'select'), array_keys($obj->_fields));
		} else {
			call_user_func_array(array($query, 'select'), array($obj->__id_field, $obj->__name_field));
		}
		foreach ($where as $key=>$value) {
			$query->where($key, '=', $value);
		}
		if ($order_by) {
			$query->order_by($order_by, $order_by_dir);
		} else if ($obj->_sort_on) {
			$query->order_by($obj->_sort_on);
		}
		
		if ($limit_offset !== null && $limit !== null) {
			$query->offset($limit_offset)->limit($limit);
		}
		
		$results = array();
		$result = $query->execute($obj->_db);
		while ($row = $result->current()) {
			array_push($results, Bamboo::factory($name, $row));
			$result->next();
		}
		return $results;
	}
	
	private function __construct()
	{
		$this->_fields = array();
	}
	
	public function __get($name)
	{
		switch ($name) {
			case '__id_field':
				return $this->_id_field;
				break;
			case '__name_field':
				return $this->_name_field;
				break;
			case '__id':
				return $this->{$this->_id_field};
				break;
			case '__name':
				return $this->{$this->_name_field};
				break;
			case '_table':
				return $this->_table;
				break;
			case '_sort_on':
				return $this->_sort_on;
				break;
			default:
				if (!isset($this->_fields[$name]))
				{
					throw new Bamboo_Exception(':name model does not have a field :field',
						array(':name' => get_class($this), ':field' => $name));
				}
				return $this->_fields[$name];
		}
	}
	
	public function __set($name, $value)
	{
		if (!isset($this->_fields[$name]))
		{
			throw new Bamboo_Exception(':name model does not have a field :field',
				array(':name' => get_class($this), ':field' => $name));
		}
		$this->_fields[$name]->set($value);
	}
	
	public function load()
	{
		$query = DB::select()
					->from($this->_table);
		foreach ($this->_fields as $field_name => $field) {
			if ($field->raw() != $field->default) {
				$query->where($field_name, '=', $field->raw());
			}
		}
		call_user_func_array(array($query, 'select'), array_keys($this->_fields));
		$result = $query->execute($this->_db);
		if ($data = $result->current()) {
			$this->values($data);
			return true;
		}
		return false;
	}
	
	public function create()
	{
		$query = DB::insert($this->_table);
		$columns = array();
		$values = array();
		foreach ($this->_fields as $field_name => $field) {
			if ($field->raw() && $field_name != $this->__id_field) {
				array_push($columns, $field_name);
				array_push($values, $field->raw());
			}
		}
		$query->columns($columns);
		$query->values($values);
		
		$result = $query->execute($this->_db);
		$id = $result[0];
		$this->{$this->_id_field} = $id;
		
		if (isset($this->_sort_on)) {
			$num_records = DB::select('*')
							->from($this->_table)
			// TODO: Sort on grouping?
							->execute($this->_db)
							->count();
			DB::update($this->_table)
				->set(
					array(
						$this->_sort_on => $num_records
					)
				)
				->where($this->_id_field, '=', $id)
				->execute($this->_db);
			/*
			$this->{$this->_sort_on} = $num_records;
			$this->update();
			*/
		}
		
		return true;
	}
	
	public function update()
	{
		$query = DB::update($this->_table)
					->where($this->__id_field, '=', $this->__id->get());
		foreach ($this->_fields as $field_name => $field) {
			if ($field_name != $this->__id_field) {
				$query->value($field_name, $field->raw());
			}
		}
		$result = $query->execute($this->_db);
		// TODO: Return the number of effected rows? Or just true?
		return true;
	}
	
	public function delete()
	{
		DB::delete($this->_table)
				->where($this->_id_field, '=', $this->__id)
				->execute($this->_db);
	}
	
	public function fields()
	{
		// TODO: copy??
		return $this->_fields;
	}
	
	public function field($name)
	{
		return $this->_fields[$name];
	}
	
	public function inputs($validationErrors = array())
	{
		$inputs = array();
		foreach ($this->_fields as $field_name => $field) {
			if ($field->editable) {
				$input = $field->input($field_name);
				if (isset($validationErrors[$field_name])) {
					$input = '<div class="error">' . $validationErrors[$field_name] . '</div>' . $input;
				}
				$inputs[$field->label] = $input;
			}
		}
		return $inputs;
	}
	
	public function isSortable()
	{
		return isset($this->_sort_on);
	}
	
	public function values(array $values = array())
	{
		foreach ($values as $field => $key) {
			if (isset($this->_fields[$field])) {
				$this->{$field} = $key;
			}
		}
	}
	
	abstract protected function _init();
    
} // End Bamboo

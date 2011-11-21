<?php 
/*
*   Artemis Framework                               					  |
* 
* @Author : Saeed Moghadam Zade

*/
class Controller extends Object
{
	/*
	* view Object
	**/
	public $view;
	
	/**
	* helpers name
	*
	**/
	protected $helper;
	/**
	* componenets name
	* 
	*/
	protected $component;
	/**
	* modles name
	*
	*/
	protected $model;
	
	/**
	* input object to manage POST and GET var
	* 
	*/
	public $input;
	
	/**
	* create $view object. check and load helpers,componenets,models
	*
	*
	**/
	function __construct()
	{
			parent::__construct();
			$this->view = new Template(str_replace('Controller','',get_class($this)));
			$this->input = new Input();
			
			if(!empty($this->helper))
			{
				$this->helper($this->helper);	
			}
			if(!empty($this->component))
			{
				$this->component($this->component);	
			}
			if(!empty($this->model))
			{
				$this->model($this->model);	
			}
			
			
	}
	/**
	* Load helpers
	* 
	* 
	**/
	private function helper($helper)
	{
		if(is_array($helper))
		{
			array_map(array('Controller','helper') , $helper);
		}
		else 
		{
			include_once(BASE.'helpers/'.$helper.'.php');
			if(!is_object($helper))
				$this->view->$helper = new $helper;
		}
	}
	/**
	* Load component
	* 
	* 
	**/	function component($com)
	{
			if(is_array($com))
		{
			array_map(array('Controller','component') , $com);
		}
		else 
		{
			include_once(BASE.'components/'.$com.'.php');
			if(!is_object($com))
				//$this->$helper =  new $helper();	
			$this->$com = new $com;
		}
	}
	
		
	
	/**
	*
	* Load Model
	*
	**/
	
	function model($model)
	{
		if(is_array($model))
		{
			array_map(array('Controller','model'),$model);
		}
		else
		{
			$file = APP_PATH.'models/'.strtolower($model).'.php';	
			if(file_exists($file))
			{
				include_once($file);
				$this->$model = new $model();
				 
			}
			else
				die($model.' Model does not exists');
		}
	}

}

<?php
/*
 *   Artemis Framework                               					  |
 *
 * @Author : Saeed Moghadam Zade

 */

require_once('Abstract/Abstract_Controller.php');

class Controller extends Abstract_Controller
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
	 * plugins name
	 *
	 */
	protected $plugin;
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
	 * create $view object. check and load helpers,plugins,models
	 *
	 *
	 **/
	function __construct()
	{
		//parent::__construct();
		$this->view = new Template(str_replace('Controller','',get_class($this)));
		$this->input = new Input();
			
		if(!empty($this->helper))
		{
			$this->helper($this->helper);
		}
		if(!empty($this->plugin))
		{
			$this->plugin($this->plugin);
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
	protected function helper($helper)
	{
		if(is_array($helper))
		{
			array_map(array('Controller','helper') , $helper);
		}
		else
		{
			include_once('View/Helper/'.$helper.'.php');
			if(!is_object($helper))
			$this->view->$helper = new $helper;
		}
	}
	/**
	 * Load plugin
	 *
	 *
	 **/
	protected function plugin($plugin)
	{
		if(is_array($plugin))
		{
			array_map(array('Controller','plugin') , $plugin);
		}
		else
		{
			include_once('Controller/Plugin/'.$plugin.'.php');
			if(!is_object($plugin))
			//$this->$helper =  new $helper();
			$this->$plugin = new $plugin;
		}
	}



	/**
	 *
	 * Load Model
	 *
	 **/

	protected function model($model)
	{
		if(is_array($model))
		{
			array_map(array('Controller','model'),$model);
		}
		else
		{
			$file = APP_PATH.'models/'.strtolower($model).'.php';
			if(!file_exists($file))
			{
				die("$model Not found");
			}
			include_once $file;
			
			$this->$model = AppModel::factory(new $model());
		}
	}
	
}

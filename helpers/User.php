<?php
/**
*
* @author Saeed moghadam
* @version 1
*
**/
class Auth extends Model
{
	public $error;
	public $table = 'users';
	
	private $user_data;
	
	function Auth()
	{
		session_start();
		parent::__construct();
		
	}
	
	function login($username , $password)
	{
		$password = md5($password);
		$user = $this->find()->where("username = '$username' AND password= '$password'");
		 
		if($user->num_rows() > 0 )
		{
			$this->set_user_data($user->fetch_assoc());
			return true;
		}else 
			return false;
	}
	
	function register($username , $password , $conf_pass)
	{
		if($this->has_user($username)) 
		{
			$this->errors("Username already exists");
			return false;
		}
		
		if($password === $conf_pass)
		{
			$data = array('username'=>$username,'password'=>md5($password),'role_id'=>3);
			if($this->create($data))
			{
				$this->save();
				return true;
			}
			$this->errors("Can not create");
			return false;
		}
		else
			$this->errors("Password does not match");
			
		return false;	
	}
	
	function logout($username)
	{
	}
	
	function has_user($u)
	{
		$user = $this->find()->where("username = '$u'");
		if($user->num_rows() > 0)
			return true;
		
		return false;	
	}
	
	function hasPermission($username , $permission = '')
	{
		
		if($this->data('username') === $username) return true;
		
		return false;
		 
	}
	
	function confirm_email($email)
	{
	}
	
	private function set_user_data($user_data = array())
	{
		if(empty($user_data)) return false;
		
		foreach($user_data as $data)
			$_SESSION['user_data'] = $data;
	}
	
	public function data($key)
	{
		if(!empty($key))
			return $_SESSION['user_data'][$key];
			
		return $_SESSION['user_data'];	
	}
	
	function errors($e)
	{
		$this->error .= $e;
		return $this->error;
	}
}
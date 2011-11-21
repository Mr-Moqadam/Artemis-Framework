<?php
/**
* Db class 
* @author Saeed Moghadam zade
*  
* created for Artemis framework
*/


include_once(ROOT.'/helpers/Validation.php');

class db extends PDO
{
	
	
	private $query;
	/**
	* store primary key field name
	*
	* @access protected
	*/
 	protected $pk  = 'id';
	
	/**
	* $fields is an array for save field name and field value
	*
	* @access private
	**/
	private $fields = array();
	
	
	/**
	*
	* store table name 
	*
	* @access protected
	*/
	protected $table ; 
	
	
	/**
	* stor validation rules
	*
	* validation rules init in model user in $validation array
	* get and validate field with Validation class in helper folder
	*
	* @access protected
	*/
	protected $validation = array();
	
	/**
	* error array 
	* 
	* stor error in $error 
	*
	* @access private
	**/
	private $error = array();
	
	
	/**
	* stor a PDO statment object
	*
	*
	* @access protected
	**/
	protected $result;
	
	
	protected $bindValues = array();
	/**
	*
	*
	**/
	protected $cu_fields ;
	/**
	* contructor : connect and select database
	*
	*
	**/
	public function __construct($table = '')
	{
		$server = SERVER ;
		$username = USERNAME;
		$password = PASSWORD;
		$database = DATABASE;
 		
		parent::__construct('mysql:host='.$server.';dbname='.$database ,$username, $password);
		$this->set_field_name();
		$this->table = strtolower($table);
	}
	
	function last_query()
	{
		return $this->query;	
	}
	/**
	* validate $values according $validation rules
	*
	* $validation rules init in user moddel
	* @access private
	*/
	private function validate($values)
	{
		$rules = $this->validation;
		
		$validate = new Validation();	
		
		$error = $validate->validateFields($values , $rules);
		
		if(!empty($error))
		{
			$this->error = $error;
			return false;
		}else{
			return true;
		}
	}

	private function set_field_name()
	{
		$table = $this->table;
		//select all fields from $this->table
		$result = $this->prepare("select * from $table");
		$result->execute(); 
		// $result;
		$i = 0 ;
		while($i < $result->columnCount())
		{
			//  $i;
			$meta = $result->getColumnMeta($i);
			$this->fields[$meta['name']]=  '' ;
			$i++; 
		}
		
	 
	}
	/**
	* Setter
	* set a field value
	*/
	public function __set($index , $value)
	{
		$this->fields[$index] = $value;
	}
	
	
	/**
	* get a field value
	**/
	public function __get($index)
	{
		return $this->fields[$index];
	}
	
	
	/**
	*
	* Find a row with use of magic method __call
	*
	* the magic method __call return a row whene user call findByTitle
	* this method find a row by title field in table
	*
	* @access public
	* 
	*/
	public function __call( $method, $args )
	{
		//print_r($args);
		if ( preg_match( "/findBy(.*)/", $method, $found ) )
		{
			$field = strtolower($found[1]);
			if ( array_key_exists(  $field, $this->fields ) )
			{
				$sql = "SELECT * FROM $this->table WHERE  $field = ?";
				$this->result = $this->prepare($sql);
				
				foreach($args as $k=>$v)
					$value[] = $v; 
				
				$this->result->execute($value);
				
				return $this->result->fetchAll(PDO::FETCH_ASSOC);
			}
		}
 

		return false;
	
	}
	

	function fetchOne()
	{
		$this->result = $this->prepare($this->query);
		
		foreach($this->bindValues as $key=>$value)
		{
			if(is_int($value))
				$this->result->bindValue($key , $value , PDO::PARAM_INT);	
			else
				$this->result->bindValue($key , $value);		
		}
	
		$this->result->execute();
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}	
 

	
	function findAll($options = array())
	{
		$sql = "SELECT * FROM $this->table";
		$this->result = $this->prepare($sql);
		$this->result->execute();
		return $this->result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function numRows()
	{
 		 
 //		print_r($this->bindValues);
		// $this->query;
		$this->result = $this->prepare($this->query);
		
		foreach($this->bindValues as $key=>$value)
		{
			if(is_int($value))
				$this->result->bindValue($key , $value , PDO::PARAM_INT);	
			else
				$this->result->bindValue($key , $value);		
		}
		
		 $this->result->execute();
		return $this->result->rowCount();	
	}

	function find($options = array())
	{
		$this->bindValues = array();
		$this->query = "SELECT * FROM $this->table" ;
		
		return $this;
	}
 
  
 	function where($where = array())
	{
		if(!is_array($where)) return false;
		
		foreach($where as $field=>$value)
		{
			$q[] = $field . ' = :'.$field;
			$fields[] = $field;
			$values[] = $value;
		}
		
		$this->query .= ' WHERE '.implode(' AND ', $q);
		//$this->result = $this->prepare($this->query);
		
		foreach($where as $field=>$value)
		{
			 
			$this->bindValues[":$field"] = $value;
		} 

		return $this;
	}
	
	function join($table , $table1 , $table2, $cond = array())
	{
		if(empty($cond))
			 $this->query .= " LEFT JOIN $table ON $table1 = $table2 ";	
		elseif(is_array($cond) AND !empty($cond))
	    {
			foreach($cond as $k=>$v)
			{
				$condition[] = $k.'='.$v;
			}
			$cond = implode(' AND ',$condition);
			$this->query .= " INNER JOIN $table ON $table1 = $table2  WHERE $cond ";
			//$this->bindValues[':join'] = $value;
		}else
		{
			$this->query .= " INNER JOIN $table ON $table1 = $table2  WHERE $cond ";	
		}
		
		return $this;
	}
	
	function order($order = '',$dir = '')
	{
	    $this->query .= " ORDER BY $order $dir ";
 
	 
		return $this;
	}

	function limit($start , $results)
	{ 
	   $this->query .= " LIMIT :start , :results ";
		$this->bindValues[':start']= $start ;
 		$this->bindValues[':results']= $results ;

		return $this;
	}
	
	
	function fetch()
	{
 		$this->result = $this->prepare($this->query);
//		 $this->query;
		foreach($this->bindValues as $key=>$value)
		{
			if(is_int($value))
				$this->result->bindValue($key , $value , PDO::PARAM_INT);	
			else
				$this->result->bindValue($key , $value);		
		}
		
		 $this->result->execute(); 
		 $this->result->errorInfo();
		 return $this->result->fetchAll(PDO::FETCH_ASSOC);	
	} 
	
	
	
	
	/**
	* create fields and value properties
	* 
	* این تابع فیلدها و مقادیر مربوط به فیلدها رو به صورت خاصیت های کلاس میسازه
	* $values یک آرایه از مقادیر و نام فیلدهاست مثل : 
	* array('title'=>'new post','body'=>new body');
	* برای اعتبار سنجی مقادیر ارسالی باید متغیر  $validarion
	* را در مدل به صورت دلخواه مقدار دهی کنیم.
	* protected $validation = array("required,title,Title is Required");
	* در صورت true بودن $xss_check مقادیر ارسالی از حملات xss پاک سازی میشن.
	*
	* @access public
	* return bool
	**/
	public function create($values = array(), $escape = false)
	{
		
		if(!is_array($values)) return false;
		try
		{
			
			if(!empty($this->validation) AND !$this->validate($values))
				return false;
				
			if($escape)
			{
				$values = $this->sanitize($values);
			}
			$this->set_field_name();
			foreach($values as $field=>$value)
			{		
				if(in_array($field , array_keys($this->fields)))
				{
						$this->cu_fields[$field] = $value;
				}
			}
 
			return true;
		}
		catch(Exception $e)
		{
			 $e->getMessage();
			return false;	
		}
		
	}
	
	/**
	*
	* درج یک ردیف در دیتابیس که به صورت زیر عمل میکنیم
	* 
	* 		if($this->Post->create($_POST))
	*		{
	*				$this->Post->insert();
	*				 "<div class='success'>New post successfully created</div>";
	*		}else
	*			$this->layout->set('error' , $this->Post->getError());c 
	*
	*/
	public function insert()
	{
		//print_r($this->cu_fields);
		foreach($this->cu_fields as $field=>$v)
			$ins[] = ':'.$field;
		
		$ins = implode(',' , $ins);
		$fields = implode(',',array_keys($this->cu_fields));
		$sql = "INSERT INTO $this->table ($fields) VALUES ($ins)";	
		
		$this->result = $this->prepare($sql);
		foreach($this->cu_fields as $f=>$v)
		{
			    $this->result->bindValue(':'.$f , $v);			
		}
		$this->result->execute();
		//print_r($this->result->errorInfo());
		// 'Insert Cat';
	}


	/**
	*
	*
	*
	*/
	public function update($pk_value = false)
	{	
		foreach($this->cu_fields as $field=>$v)
		{
			if($field !== $this->pk)
			{
				$up[] = $field.'= :'.$field;
			    //$values[] = $v;		
			}
		}
		
		$up = implode(',' , $up);
	 
		  $sql = "UPDATE $this->table SET $up WHERE $this->pk = :$this->pk";	
 		$this->cu_fields[$this->pk] = $pk_value;
 

		$this->result = $this->prepare($sql);
		
		foreach($this->cu_fields as $f=>$v)
		{
			$this->result->bindValue(':'.$f , $v);			
		}
		
		$this->result->execute();
		 
	}
	
 	function delete($pkValue = 0)
	{
		if($pkValue == 0) return false;
		
		$sql = "DELETE FROM $this->table WHERE $this->pk = $pkValue";
		$this->result = $this->prepare($sql);
		$this->result->execute();
		return true;	
	}
	/**
	* Get Errors
	* 
	**/
	public function getError()
	{
		return implode('</br>' , $this->error);
	}
	
	/**
	*
	* Check and clean data from xss attack
	*
	*
	**/
	private function sanitize($input) {
		if (is_array($input)) {
			foreach($input as $var=>$val) {
				$output[$var] = $this->sanitize($val);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
			$input  = $this->clean($input);
			$output = mysql_real_escape_string($input);
		}
		return $output;
	}
	
	
	function clean($str) 
	{
		if(is_array($str))
			array_map(array('Input','clean'),$str);
		if(!get_magic_quotes_gpc()) {
			$str = addslashes($str);
		}
			$str = strip_tags(htmlspecialchars($str));
		return $str;
	}


}
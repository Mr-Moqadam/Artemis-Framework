<?php
class AppModel extends db
{
	function AppModel()
	{
		parent::__construct(get_class($this));	
	}
}
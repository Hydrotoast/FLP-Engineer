<?php
class Config
{
	private $db;
	private $table;
	
	function Config() {
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Error connection to database. \n');
		$this->table = DB_EXT . '_config';
	}
	
	function get_active_flp()
	{
		$query = 'SELECT config_value FROM ' . $this->table . ' WHERE config_key=\'active_flp\' LIMIT 1';
		$result = $this->db->query($query);

		return $result;
	}
	
	function activate_flp($file)
	{
		if(!is_dir($file))
		{
			$file = $this->db->real_escape_string($file);
			$query = 'UPDATE ' . $this->table . ' SET config_value=\'' . $file . '\' WHERE config_key=\'active_flp\';';
			$query .= 'UPDATE ' . $this->table . ' SET config_value=\'1\' WHERE config_key=\'flp_changed\';';
			$this->db->multi_query($query);
			$this->db->close();
		}
	}
	
	function __destruct()
	{
		$this->db->close();
	}
}
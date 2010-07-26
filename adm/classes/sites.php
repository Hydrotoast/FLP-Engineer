<?php
class Sites
{
	private $db;
	private $table;
	
	function Sites() {
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Error connection to database. \n');
		$this->table = DB_EXT . '_sites';
	}
	
	function get_sites()
	{
		$query = 'SELECT * FROM ' . $this->table . ' ORDER BY site_id DESC';
		$result = $this->db->query($query);
		
		return $result;
	}
	
	function activate_site($id)
	{
		$query = 'UPDATE ' . $this->table . ' SET site_active=0 WHERE site_active=1;';
		$query .= 'UPDATE ' . $this->table . ' SET site_active=1 WHERE site_id=' . (int) $id . ';';
		$this->db->multi_query($query);
	}
	
	function add_site($redirect)
	{	
		$query = 'INSERT INTO ' . $this->table . ' (site_redirect) VALUES (\'' . $redirect . '\')';
		$this->db->query($query);
	}
	
	function delete_site($id)
	{
		$query = 'DELETE FROM ' . $this->table . ' WHERE site_id=' . (int) $id;
		$this->db->query($query);
	}
	
	function __destruct()
	{
		$this->db->close();
	}
}
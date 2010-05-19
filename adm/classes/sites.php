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
		$result->free_result();
		$this->db->close();
	}
	
	function activate_site($id)
	{
		$query = 'UPDATE ' . $this->table . ' SET site_active=0 WHERE site_active=1;';
		$query .= 'UPDATE ' . $this->table . ' SET site_active=1 WHERE site_id=' . $id . ';';
		$this->db->multi_query($query);
		$this->db->close();
	}
	
	
	function add_site($redirect)
	{
		$redirect = filter_var($redirect, FILTER_SANITIZE_URL);
		$query = 'INSERT INTO ' . $this->table . ' (site_redirect) VALUES (\'' . $redirect . '\')';
		$this->db->query($query);
		$this->db->close();
	}
	
	function delete_site($id)
	{
		$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
		$query = 'DELETE FROM ' . $this->table . ' WHERE site_id=' . (int)$id;
		$this->db->query($query);
		$this->db->close();
	}
}
<?php
class Logs
{
	private $db;
	private $table;
	
	private $logs_per_page = 25;
	
	function Logs() 
	{
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Error connection to database. \n');
		$this->table = DB_EXT . '_users';
	}
	
	function get_logs($page = 0)
	{
		$offset = $page * $this->logs_per_page; // Offsets the limit for the current page
		$query = 'SELECT * FROM ' . $this->table . ' ORDER BY user_id DESC LIMIT ' . $offset . ', ' . $this->logs_per_page;
		$result = $this->db->query($query);
		
		return $result;
	}
	
	function get_total_pages()
	{
		$query = 'SELECT COUNT(*) AS total FROM ' . $this->table;
		$result = $this->db->query($query);
		$row = $result->fetch_assoc();
		
		return ceil($row['total']/$this->logs_per_page);
	}
	
	function delete_logs($ids)
	{
		$query = '';
		foreach($ids as $id)
		{
			$query .= 'DELETE FROM ' . $this->table . ' WHERE user_id=' . (int) $id . ';';
		}
		
		$this->db->multi_query($query);
	}
	
	function __destruct()
	{
		$this->db->close();
	}
}
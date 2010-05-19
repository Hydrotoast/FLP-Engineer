<?php
class Logs
{
	private $db;
	private $table;
	
	function Logs() {
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Error connection to database. \n');
		$this->table = DB_EXT . '_users';
	}
	
	function get_logs($page = 0)
	{
		$logs_per_page = 25;
		if($page === 0)
		{
			$query = 'SELECT COUNT(*) FROM ' . $this->table . ' ORDER BY user_id DESC';
			$result = $this->db->query($query);
			$row = $result->fetch_assoc();
			
			return ceil($row['COUNT(*)']/$logs_per_page);
			$result->free_result();
			$this->db->close();
		}
		else
		{	
			$offset = ($page - 1) * $logs_per_page; // Offsets the limit for the current page
			$query = 'SELECT * FROM ' . $this->table . ' ORDER BY user_id DESC LIMIT ' . $offset . ', ' . $logs_per_page;
			$result = $this->db->query($query);
			
			return $result;
			$result->free_result();
			$this->db->close();
		}
	}
	
	function delete_logs($ids)
	{
		$query = '';
		foreach($ids as $id)
		{
			$query .= 'DELETE FROM ' . $this->table . ' WHERE user_id=' . (int)$id . ';';
		}
		
		$this->db->multi_query($query);
		$this->db->close();
	}
}
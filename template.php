<?php
DEFINE('CACHE_FILE', 'template/cache.php');

class Template {
	var $page;
	
	/* Initializes a template
	 * @param $file The file to retrieve template data from
	 * @param $tags A tag array with their respective keys to be replaced
	 * @param $flp_changed Check if the FLP template was changed
	 */
	function Template($file, $tags = array(), $flp_changed = 0) {
		if($flp_changed == 1) {
			$this->clear_cache();
		}
		
		if(file_exists(CACHE_FILE)) {
			$file = @fopen(CACHE_FILE, 'r');
			
			if(is_readable(CACHE_FILE))
			{
				$this->page = @fread($file, filesize(CACHE_FILE));
				fclose($file);
			}
		}
		elseif(file_exists($file)) {
			// Assign the page to a local variable
			$page = file_get_contents($file);
			
			// Replace template tags in the page
			foreach($tags as $tag => $value) {
				$page = preg_replace('/{' . $tag . '}/', $value, $page);
			}
			
			$cache_file = @fopen(CACHE_FILE, 'w');
			
			if(is_writable(CACHE_FILE)) {
				@fwrite($cache_file, $page, strlen($page));
				fclose($cache_file);
				
				$file = @fopen(CACHE_FILE, 'r');
				
				if(is_readable(CACHE_FILE))
				{
					$this->page = @fread($file, filesize(CACHE_FILE));
					fclose($file);
				}
			}
		}
		else {
			die('File does not exist.');
		}
	}
	
	function clear_cache() {
		unlink(CACHE_FILE);
	}
	
	/* Outputs the template
	 */
	function output() {
		echo $this->page;
	}
}
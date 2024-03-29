<?php

class File
{
	private $path = null;
	private $file_name = null;
	private $extension = null;
	
	/**
	 * Constructor Set path, file_name and extension
	 */
	public function __construct( $filename )
	{
		$this->path = $this->get_path_from_str($filename);
		$this->file_name = $this->get_file_name_from_str($filename);
		$this->extension = $this->get_extension_from_str($filename);
	}
	
	/**
	 * @return Full File Path
	 */ 
	public function get_total_path()
	{
		if ($this->extension == "")
			return $this->path . $this->file_name . $this->extension;
		return $this->path . $this->file_name . "." . $this->extension;
	}
	
	/**
	 * @return File Path
	 */
	public function get_path()
	{
		return $this->path;
	}
	
	/**
	 * @return File name
	 */
	public function get_file_name()
	{
		return $this->file_name;
	}
	
	/**
	 * @return File Extension
	 */
	public function get_extension()
	{
		return $this->extension;
	}
	
	/**
	 * @param total_path Full path from some file
	 * @return File path
	 */
	public function get_path_from_str($total_path)
	{
		$array_path = explode("/",$total_path);
		$path_str = "";
		for( $i = 0; $i < sizeof($array_path) - 1; $i++){
			$path_str .= $array_path[$i] . "/";
		}
		return $path_str;
	}
	
	/**
	 * @param total_path Full path from some file
	 * @return File name
	 */
	public function get_file_name_from_str($total_path)
	{
		$array_path = explode("/",$total_path);
		if (sizeof($array_path) == 0) return "";
		$file_name_ext = $array_path[sizeof($array_path) - 1];
		$file_name_w_ext = explode(".", $file_name_ext);
		return $file_name_w_ext[0];
	}
	
	/**
	 * @param total_path Full path from some file
	 * @return File extension
	 */
	public function get_extension_from_str($total_path)
	{
		$file_total_name = explode(".", $total_path);
		if (sizeof($file_total_name) <= 1) return "";
		
		return $file_total_name[sizeof($file_total_name) - 1];
	}
	
	
	/**
	 * Remove the actual file
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function remove_file()
	{
		$total_path = $this->get_total_path();
		if (file_exists($total_path)) {
			
			unlink($total_path);
		} else {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Remove any file from some path
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function remove_file_from_path($file_path)
	{
		
		if (file_exists($file_path)) {
			
			unlink($file_path);
		} else {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * @param file_content File content
	 * @return TRUE if file was successfuly written or FALSE otherwise
	 */
	public function write($file_content)
	{
		$total_path = $this->get_total_path();
		$handle = fopen($total_path, 'w+b');
		if (!$handle) return FALSE;
		
		$data = $file_content;
		
		if (!fwrite($handle, $data)) return FALSE;
		
		return TRUE;
	}
	
	/**
	 * Write some content to some file
	 * @param file_total_path Path from some file
	 * @param file_content File content
	 * @return TRUE if file was successfuly written or FALSE otherwise
	 */
	public function write_to_file($file_total_path, $file_content)
	{
		$handle = fopen($file_total_path,'w+b');
		if (!$handle) return FALSE;
		
		$data = $file_content;
		
		if (!fwrite($handle, $data)) return FALSE;
		
		return TRUE;
	}
	
	/**
	 * Read content from this file
	 */
	public function read()
	{
		$total_path = $this->get_total_path();
		$handle = fopen($total_path, 'r');
		$data = fread($handle, filesize($total_path));
		
		return $data;
	}
	
	/**
	 * Read content from some file
	 * @param file_total_path Total path from some file
	 */
	public function read_from_file($file_total_path)
	{
		$handle = fopen($file_total_path,'r');
		$data = fread($handle, filesize($file_total_path));
		
		return $data;
	}
	
	/**
	 * Move from some file to this file
	 * @param file_name Total path from some file
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function move_from($file_name)
	{
		$file_content = $this->read_from_file($file_name);
		if (!$this->write($file_content)) return FALSE;
		
		return TRUE;
	}
	
	/**
	 * Move from this file to some file
	 * @param file_name Total path from some file
	 * @return TRUE if successfull or FALSE otherwise
	 */
	public function move_to($file_name)
	{
		$file_content = $this->read();
		if (!$this->write_to_file($file_name, $file_content)) return FALSE;
		
		return TRUE;
	}
	
};

?>

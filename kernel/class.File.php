<?php

class File
{
	private $path = null;
	private $file_name = null;
	private $extension = null;
	public function __construct( $filename )
	{
		$this->path = $this->get_path_from_str($filename);
		$this->file_name = $this->get_file_name_from_str($filename);
		$this->extension = $this->get_extension_from_str($filename);
	}
	
	public function get_total_path()
	{
		return $this->path . $this->file_name . $this->extension;
	}
	
	public function get_path()
	{
		return $path;
	}
	
	public function get_file_name()
	{
		return $file_name;
	}
	
	public function get_extension()
	{
		return $extension;
	}
	
	public function get_path_from_str($total_path)
	{
		$array_path = explode("/",$total_path);
		$path_str = "";
		for( $i = 0; $i < sizeof($array_path) - 1; $i++){
			$path_str .= $array_path[$i] . "/";
		}
		return $path_str;
	}
	
	public function get_file_name_from_str($total_path)
	{
		$array_path = explode("/",$total_path);
		if (sizeof($array_path) == 0) return "";
		$file_name_ext = $array_path[sizeof($array_path) - 1];
		return $file_name_ext;
	}
	
	public function get_extension_from_str($total_path)
	{
		$file_total_name = $this->get_file_name_from_str($total_path);
		if ($file_total_name === "") return $file_total_name;
		
		$array_file_name = explode(".", $file_total_name);
		
		if (sizeof($array_file_name) <= 1) return "";
		
		return $array_file_name[1];
	}
	
	public function write($file_content)
	{
		$total_path = $this->get_total_path();
		$handle = fopen($total_path, 'w');
		if (!$handle) return FALSE;
		
		$data = $file_content;
		
		if (!fwrite($handle, $data)) return FALSE;
		
		return TRUE;
	}
	
	public function write_to_file($file_total_path, $file_content)
	{
		$handle = fopen($file_total_path,'w');
		if (!$handle) return FALSE;
		
		$data = $file_content;
		
		if (!fwrite($handle, $data)) return FALSE;
		
		return TRUE;
	}
	
	public function read()
	{
		$total_path = $this->get_total_path();
		$handle = fopen($total_path, 'r');
		$data = fread($handle, filesize($total_path));
		
		return $data;
	}
	
	public function read_from_file($file_total_path)
	{
		$handle = fopen($file_total_path,'r');
		$data = fread($handle, filesize($file_total_path));
		
		return $data;
	}
	
	public function move_from($file_name)
	{
		$file_content = $this->read_from_file($file_name);
		if (!$this->write($file_content)) return FALSE;
		
		return TRUE;
	}
	
	public function move_to($file_name)
	{
		$file_content = $this->read();
		if (!$this->write_to_file($file_name, $file_content)) return FALSE;
		
		return TRUE;
	}
	
};

?>

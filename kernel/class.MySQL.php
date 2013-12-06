<?php

class MySQL
{
	
	private static $mysql_link = false;
	private static $was_db_selected = false;
	private static $last_query = false;

	/**
	 * Construtor de objeto. É necessário criar um objeto para inicializar a classe (e se conectar ao servidor SQL).
	 * Assim, pode-se tirar vantagem do destrutor, para que a conexão com o host SQL seja fechada automaticamente.
	 */
	public function __construct($host, $username, $password, $dbase)
	{
		if (self::$mysql_link = @mysql_connect($host, $username, $password))
			if (self::$was_db_selected = @mysql_select_db($dbase, self::$mysql_link))
			{
				mysql_query("SET NAMES 'utf8'");
				//mysql_query("SET character_set_connection=utf8");
				//mysql_query("SET character_set_client=utf8");
				//mysql_query("SET character_set_results=utf8");
			}
	}

	/**
	 * Ao destruir o objeto, fecha a conexão com o host SQL.
	 */
	public function __destruct()
	{
		if (self::$mysql_link)
			mysql_close(self::$mysql_link);
	}

	/**
	 * Informa se a classe está conectada ao servidor SQL (true/false).
	 */
	public static function is_connected()
	{
		return self::$mysql_link && self::$was_db_selected;
	}

	/**
	 * Executa uma query MySQL.
	 * Para $auto_free=false, armazena o resource da query em self::$last_query.
	 * Para $auto_free=true, libera o resource depois de executar a query. Use-a caso não precise do resource posteriormente.
	 * Retorna o resource da query executada (caso $auto_free=true, use-o apenas para saber se a query funcionou corretamente).
	 */
	public static function query($query, $auto_free=false)
	{
		self::$last_query = $resource = mysql_query($query, self::$mysql_link);
		if ($auto_free)
			self::free();
		return $resource;
	}

	/**
	 * Obtém os resultados de uma query SQL e armazena-os em um array unidimensional ($as_array=false) ou bidimensional ($as_array=true).
	 * Retorna as linhas lidas (em um array [<chave>]=<valor> ($as_array=false) ou [<linha>][<chave>]=<valor> ($as_array=true)), ou false (0 linhas lidas).
	 */
	public static function fetch($query, $as_array=false, $auto_free=true)
	{
		$rows = false;
		if (is_string($query))
			$query = self::query($query);
		if (is_resource($query))
		{
			if ($as_array)
				while ($row = mysql_fetch_assoc($query))
					$rows[] = $row;
			else
				$rows = mysql_fetch_assoc($query);

			if ($auto_free)
				self::free($query);
		}
		return $rows;
	}

	public static function fetch_array($query, $auto_free=true)
	{
		return self::fetch($query, true, $auto_free);
	}

	/**
	 * Informa a quantidade de linhas retornadas por uma query; ou false, se não for uma query válida.
	 */
	public static function num_rows($query, $auto_free=true)
	{
		if (is_string($query))
			$query = self::query($query);
		if (is_resource($query))
		{
			$num_rows = mysql_num_rows($query);
			if ($auto_free)
				self::free($query);
			return $num_rows;
		}
		return false;
	}

	/**
	 * Libera a memória usada por uma query.
	 */
	public static function free($resource=null)
	{
		if ($resource === null || $resource == self::$last_query)
		{
			$resource = self::$last_query;
			self::$last_query = null;
		}
		return is_resource($resource) ? mysql_free_result($resource) : false;
	}

	/**
	 * Insere uma linha em uma tabela e retorna um resource para a query de inserção.
	 * $data deve ser um array no formato [<chave>]=<valor>.
	 */
	public static function insert($table, &$data, $auto_free=true)
	{
		// monta a string das chaves (`key1`,`key2`,...) e valores ('value1','value2',...)
		$keys = $values = "";
		foreach ($data as $key => $value)
		{
			$keys .= "`$key`,";
			$values .= ($value === null ? "null," : "'" . mysql_real_escape_string($value) . "',");
		}
		$keys = rtrim($keys, ",");
		$values = rtrim($values, ",");

		return self::query("INSERT INTO `$table` ($keys) VALUES ($values)", $auto_free);
	}

	/**
	 * Insere várias linhas em uma tabela e retorna um resource para a query de inserção.
	 * $data deve ser um array bidimensional, onde cada linha é um array no formato [<chave>]=<valor>.
	 * (english) Inserts multiple lines in a table and returns a resource for the INSERT query.
	 * (english) $data is an array of associative arrays: $data_array[<line>][<column name>] = <value>.
	 */
	public static function multi_insert($table, &$data_array, $auto_free=true)
	{
		if (sizeof($data_array) == 0)
			return false;

		// monta o mapa das chaves
		$keys_map = array();
		foreach ($data_array as $data)
			foreach ($data as $key => $value)
				$keys_map[$key] = true;

		// monta a string das chaves
		$keys = "";
		foreach ($keys_map as $key => $value)
			$keys .= "`$key`,";
		$keys = rtrim($keys, ",");

		// monta a string dos valores
		$values = "";
		foreach ($data_array as $data)
		{
			$values .= "(";
			foreach ($keys_map as $key => $value)
			{
				if (array_key_exists($key, $data) && $data[$key] !== null)
					$values .= "'" . mysql_real_escape_string($data[$key]) . "',";
				else
					$values .= "null,";
			}
			$values = rtrim($values, ",") . "),";
		}
		$values = rtrim($values, ",");

		return self::query("INSERT INTO `$table` ($keys) VALUES $values", $auto_free);
	}

	/**
	 * Após inserir uma linha em uma tabela, pode ser usado para saber o valor do id inserido.
	 */
	public static function insert_id()
	{
		return mysql_insert_id(self::$mysql_link);
	}

	/**
	 * Retorna a quantidade de linhas afetadas por uma query do tipo INSERT, UPDATE, REPLACE ou DELETE.
	 */
	public static function affected_rows()
	{
		return mysql_affected_rows(self::$mysql_link);
	}

	/**
	 * Deleta linhas de uma tabela.
	 */
	public static function delete($table, $clauses="", $auto_free=true)
	{
		return self::query("DELETE FROM `$table` $clauses", $auto_free);
	}

	/**
	 * Atualiza os dados de uma tabela.
	 */
	public static function update($table, &$data, $clauses="", $auto_free=true)
	{
		$update = "";
		foreach ($data as $key => $value)
			$update .= "`$key`=" . ($value === null ? "null," : "'" . mysql_real_escape_string($value) . "',");
		$update = rtrim($update, ",");

		if (!empty($update))
			return self::query("UPDATE `$table` SET $update $clauses", $auto_free);
		return false;
	}

	/**
	 * Elimina as chaves inúteis de um array de dados.
	 */
	public static function clear_data(&$data, $columns, $remove_id=true)
	{
		foreach ($data as $key => $value)
			if (!array_key_exists($key, $columns) || ($key == "id" && $remove_id))
				unset($data[$key]);
	}
};

$sql = new MySQL("127.0.0.1", "root", "", "opijudge");
if (!MySQL::is_connected())
	die("Couldn't connect to sql server.");
<<<<<<< HEAD
?>
=======

?>
>>>>>>> 4ef7d21211ab90d8a311263f2c188da947dd3a57

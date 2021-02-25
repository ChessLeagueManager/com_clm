<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
class clm_class_db {
	// Verwendete Datenbank Konfiguration
	private $dbConfig;
	// die Verbindung selbst
	private $db;
	// enthält verwendete Tabellen / kann nachträglich erweitert werden (public)
	private $dbTable;
	// enthält db_table für einzelne Tabellenzugriffe
	private $data;
	private $config;
	private $config_order;
	public function __construct($dbConfig, $dbTable, $config) {
		$this->dbConfig = $dbConfig;
		$this->open();
		$this->data = array();
		$this->dbTable = $dbTable;
		$this->config = new clm_class_config($config);
	}
	public function __get($name) {
		// gibt es die Tabelle überhaupt
		if (!isset($this->dbTable[$name])) {
			return false;
		}
		// hat sie bereits eine Verbindung -> sonst stelle sie her
		if (!isset($this->data[$name])) {
			$this->data[$name] = new clm_class_db_table($this->dbTable[$name], $this->db, "clm_" . $name); // alle unsere Tabellen haben den Prefix clm_
			
		}
		// anfrage an db_table weiterleiten
		return $this->data[$name];
	}
	// schreibe alle Änderungen --> weitergabe an geöffnete Tabellen
	public function write() {
		foreach ($this->data as $value) {
			$value->write();
		}
	}
	// Eine Verbindung öffnen
	private function open() {
		mysqli_report(MYSQLI_REPORT_STRICT); 
		$db[0] = new mysqli($this->dbConfig[0], $this->dbConfig[2], $this->dbConfig[3], $this->dbConfig[1]);
		/* check connection */
		if (mysqli_connect_errno()) {
    		printf("CLM db.php: Connect failed: %s\n", mysqli_connect_error());
    		exit();
		}
		$db[0]->set_charset("utf8");
		$db[0]->query("SET SQL_BIG_SELECTS=1");
		$db[1] = $this->dbConfig[4];
		$this->db = $db;
	}
	public function query($query) {
		if (!$result = $this->db[0]->query(clm_core::$load->db_add_prefix($query, $this->db[1]))) {
		    clm_core::addError("sql invalid",$query." (".$this->db[0]->error.")"." Backtrace: ".clm_core::getBacktrace());
		} else {
			return $result;
		}
	}
	public function escape($string) {
		return $this->db[0]->real_escape_string($string);
	}
	public function prepare($query) {
		return $this->db[0]->prepare(clm_core::$load->db_add_prefix($query, $this->db[1]));
	}
	public function insert_id() {
		return $this->db[0]->insert_id;
	}
	public function affected_rows() {
		return $this->db[0]->affected_rows;
	}
	
	/**
	 * Get the current query object or a new FOFDatabaseQuery object.
	 *
	 * @param   boolean  $new  False to return the current query object, True to return a new FOFDatabaseQuery object.
	 *
	 * @return  FOFDatabaseQuery  The current query object or a new object extending the FOFDatabaseQuery class.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function getQuery($new = false)
	{
		if ($new)
		{
			// Derive the class name from the driver.
			$class = 'FOFDatabaseQuery' . ucfirst($this->name);

			// Make sure we have a query class for this driver.
			if (!class_exists($class))
			{
				// If it doesn't exist we are at an impasse so throw an exception.
				throw new RuntimeException('Database Query Class not found.');
			}

			return new $class($this);
		}
		else
		{
			return $this->sql;
		}
	}

	/**
	 * Sets the SQL statement string for later execution.
	 *
	 * @param   mixed    $query   The SQL statement to set either as a FOFDatabaseQuery object or a string.
	 * @param   integer  $offset  The affected row offset to set.
	 * @param   integer  $limit   The maximum affected rows to set.
	 *
	 * @return  FOFDatabaseDriver  This object to support method chaining.
	 *
	 * @since   11.1
	 */
	public function setQuery($query, $offset = 0, $limit = 0)
	{
		$this->sql = $query;

		if ($query instanceof FOFDatabaseQueryLimitable)
		{
			if (!$limit && $query->limit)
			{
				$limit = $query->limit;
			}

			if (!$offset && $query->offset)
			{
				$offset = $query->offset;
			}

			$query->setLimit($limit, $offset);
		}
		else
		{
			$this->limit = (int) max(0, $limit);
			$this->offset = (int) max(0, $offset);
		}

		return $this;
	}


	/**
	 * Quotes and optionally escapes a string to database requirements for use in database queries.
	 *
	 * @param   mixed    $text    A string or an array of strings to quote.
	 * @param   boolean  $escape  True (default) to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @note    Accepting an array of strings was added in 12.3.
	 * @since   11.1
	 */
	public function quote($text, $escape = true)
	{
		if (is_array($text))
		{
			foreach ($text as $k => $v)
			{
				$text[$k] = $this->quote($v, $escape);
			}

			return $text;
		}
		else
		{
			return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
		}
	}


	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * @param   mixed  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                        Each type supports dot-notation name.
	 * @param   mixed  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                        same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  mixed  The quote wrapped name, same type of $name.
	 *
	 * @since   11.1
	 */
	public function quoteName($name, $as = null)
	{
		if (is_string($name))
		{
			$quotedName = $this->quoteNameStr(explode('.', $name));

			$quotedAs = '';

			if (!is_null($as))
			{
				settype($as, 'array');
				$quotedAs .= ' AS ' . $this->quoteNameStr($as);
			}

			return $quotedName . $quotedAs;
		}
		else
		{
			$fin = array();

			if (is_null($as))
			{
				foreach ($name as $str)
				{
					$fin[] = $this->quoteName($str);
				}
			}
			elseif (is_array($name) && (count($name) == count($as)))
			{
				$count = count($name);

				for ($i = 0; $i < $count; $i++)
				{
					$fin[] = $this->quoteName($name[$i], $as[$i]);
				}
			}

			return $fin;
		}
	}

	/**
	 * Quote strings coming from quoteName call.
	 *
	 * @param   array  $strArr  Array of strings coming from quoteName dot-explosion.
	 *
	 * @return  string  Dot-imploded string of quoted parts.
	 *
	 * @since 11.3
	 */
	protected function quoteNameStr($strArr)
	{
		$parts = array();
		$q = $this->nameQuote;

		foreach ($strArr as $part)
		{
			if (is_null($part))
			{
				continue;
			}

			if ($q === false)		// Verhindern von Notice: Trying to access array offset on value of type bool in ... ab php 7.4
			{
				$parts[] = $part;
			}
			elseif (strlen($q) == 1)
//			if (strlen($q) == 1)
			{
				$parts[] = $q . $part . $q;
			}
			else
			{
				//$parts[] = $q{0} . $part . $q{1};
				$parts[] = $q[0] . $part . $q[1];
			}
		}

		return implode('.', $parts);
	}

	/**
	 * Inserts a row into a table based on an object's properties.
	 *
	 * @param   string $table   The name of the database table to insert into.
	 * @param   object &$object A reference to an object whose public properties match the table fields.
	 * @param   string $key     The name of the primary key. If provided the object property is updated.
	 *
	 * @return  boolean    True on success.
	 */
	public function insertObject($table, &$object, $key = null)
	{
		$fields = array();
		$values = array();

		// Iterate over the object variables to build the query fields and values.
		foreach (get_object_vars($object) as $k => $v)
		{
			// Only process non-null scalars.
			if (is_array($v) or is_object($v) or $v === null)
			{
				continue;
			}

			// Ignore any internal fields.
			if ($k[0] == '_')
			{
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			$fields[] = $this->quoteName($k);
			$values[] = $this->quote($v);
		}

		$str_values=implode(',', $values);
 
		// Create the base insert statement.
		$query = "INSERT INTO ".$table." ( ";
		$ic = 0;
		foreach ($fields as $field) {
			if ($ic > 0) $query .= ",";
			$ic++;
			$query .= "`".$field."`";
		}
		$query .= " ) VALUES ( ".$str_values." )";

		clm_core::$db->query($query);
		$new_tid = clm_core::$db->insert_id();

		// Update the primary key if it exists.
		$id = $new_tid;		// $this->insertid();
		if ($key && $id && is_string($key))
		{
			$object->$key = $id;
		}

		return true;
	}

	/**
	 * Updates a row in a table based on an object's properties.
	 *
	 * @param   string  $table   The name of the database table to update.
	 * @param   object  &$object A reference to an object whose public properties match the table fields.
	 * @param   string  $key     The name of the primary key.
	 * @param   boolean $nulls   True to update null fields or false to ignore them.
	 *
	 * @return  boolean  True on success.
	 */
	public function updateObject($table, &$object, $key, $nulls = false)
	{
		$fields = array();
		$where = array();

		if (is_string($key))
		{
			$key = array($key);
		}

		if (is_object($key))
		{
			$key = (array)$key;
		}

		// Create the base update statement.
		$statement = 'UPDATE ' . $this->quoteName($table) . ' SET %s WHERE %s';

		// Iterate over the object variables to build the query fields/value pairs.
		foreach (get_object_vars($object) as $k => $v)
		{
			// Only process scalars that are not internal fields.
			if (is_array($v) or is_object($v) or $k[0] == '_')
			{
				continue;
			}

			// Set the primary key to the WHERE clause instead of a field to update.
			if (in_array($k, $key))
			{
				$where[] = $this->quoteName($k) . '=' . $this->quote($v);
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			if ($v === null)
			{
				// If the value is null and we want to update nulls then set it.
				if ($nulls)
				{
					$val = 'NULL';
				}
				// If the value is null and we do not want to update nulls then ignore this field.
				else
				{
					continue;
				}
			}
			// The field is not null so we prep it for update.
			else
			{
				$val = $this->quote($v);
			}

			// Add the field to be updated.
			$fields[] = $this->quoteName($k) . '=' . $val;
		}

		// We don't have any fields to update.
		if (empty($fields))
		{
			return true;
		}

		// Set the query and execute the update.
		$str_fields = implode(",", $fields);
		$str_where = implode(",", $where);
		$query = "UPDATE ".$this->quoteName($table)." SET ".$str_fields." WHERE ".$str_where;
		$result = clm_core::$db->query($query);
		return true;
//		return $this->execute();
	}

	
	// gibt die Ergebnisse als Objekte in einem Array zurück
	public function loadObjectList($query) {
		// echo $query . "<br/><br/>";
		$result = $this->query($query);
		// printf("Errorcode: %d\n", $this->db[0]->errno);
		$array = array();
		while ($row = $result->fetch_object()) {
			$array[] = $row;
		}
		$result->close();
		return $array;
	}
	// gibt das Ergebnis als Objekte zurück
	public function loadObject($query) {
		// echo $query . "<br/><br/>";
		$result = $this->query($query);
		// printf("Errorcode: %d\n", $this->db[0]->errno);
		while ($row = $result->fetch_object()) {
			$result->close();
			return $row;
		}
		$result->close();
		return null;
	}
	// gibt die Ergebnisse als Objekte in einem Array zurück
	public function loadAssocList($query) {
		// echo $query . "<br/><br/>";
		$result = $this->query($query);
		// printf("Errorcode: %d\n", $this->db[0]->errno);
		$array = array();
		while ($row = $result->fetch_array()) {
			$array[] = $row;
		}
		$result->close();
		return $array;
	}
	// gibt die Anzahl der Ergebnisse zurück, es sollte Count im sql Query verwendet werden
	public function count($query) {
		$result = $this->query($query);
		$count = $result->fetch_row();
		$result->close();
		return $count[0];
	}
	// gibt den Datenbank Prefix zurück
	public function prefix() {
		return $this->db[1];
	}
	public function connection() {
		return $this->db[0];
	}
	// Verbindet die Views mit den Daten der Models über ein Objekt
	public function initModel() {
		return new clm_class_db_model();
	}
	public function config() {
		return $this->config;
	}
	public function config_order() {
		if (is_null($this->config_order)) {
			include (clm_core::$path . "/includes/config_order.php");
			$this->config_order = $config_order;
		}
		return $this->config_order;
	}
}
?>

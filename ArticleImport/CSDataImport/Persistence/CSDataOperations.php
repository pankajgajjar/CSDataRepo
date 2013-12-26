<?php
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Referenced Libraries/rb.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Persistence/ICSDataOperation.php';

/**
 * Class to Abstract Red Bean Library and Implement all important functions.
 * @author Anindya Gangakhedkar
 */

//Setting up the Database Connection.

//DbConstants Definition
define('dbprefix', "csliveTest130");
define('host',"localhost");
define('dbname',"cslivetrunk");
define('username',"root");
define('password','');

//R::setup('mysql:host='.constant('host').';dbname='.constant('dbname'),constant('username'),constant('password'));

class CSDataOperations implements ICSDataOperations
{
	
	public function __construct()
	{
		R::setup('mysql:host='.constant('host').';dbname='.constant('dbname'),constant('username'),constant('password'));
	}
	
	/**
	 * Gets All the rows associated with the query in a nested Array.
	 * @param $string is the query String (SQL).
	 * @return $result is the result of the query in nested Array form.
	 */
	public function getAll($string)
	{
		$result = R::getAll($string);
		return $result;
	}
	
	/**
	 * Stores an array collection of beans
	 * @param $beans is an array of beans
	 * @return returns the resultant IDS in array form.
	 */
	public function storeAll($beans)
	{
		$result = R::storeAll($beans);
		return $result;
	}
	
	/**
	 * Stores a single Bean Object into the DataBase
	 * @param $bean contains bean object to be stored.
	 * @return $result is the resultant id of the bean.
	 */
	public function store($bean)
	{
		$result = R::store($bean);
		return $result;
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $objectName
	 * @return Ambigous <multitype:, RedBean_OODBBean, mixed>
	 */
	public function dispenseBeanObject($objectName)
	{
		$bean = R::dispense($objectName);
		return $bean;
	} 
	
	/**
	 * Deletes the Table from the Database
	 * @param $tableName string of the name of the table.
	 */
	public function deleteTable($tableName)
	{
		R::wipe($tableName);
	}
	
	/**
	 * gets Single Column From The DataBase
	 * @param  $string is the string of the query(SQL)
	 * @return returns the array of the colum selected.
	 */
	public function getColumn($string)
	{
		$result = R::getCol($string);
		return $result;
	}
		
	
	/**
	 * Executes the given SQL command
	 * @param  $string the SQL string to be executed.
	 */
	public function exec($string)
	{
		R::exec($string);
	}
}
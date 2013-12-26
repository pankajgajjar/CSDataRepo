<?php

/**
 * Interface To Define All Data Operations.
 * @author Anindya Gangakhedkar
 *
 */
interface ICSDataOperations
{
	public function getAll($string);
	public function storeAll($beans);
	public function store($bean);
	public function dispenseBeanObject($objectName);
	public function deleteTable($tableName);
	public function getColumn($string);
}
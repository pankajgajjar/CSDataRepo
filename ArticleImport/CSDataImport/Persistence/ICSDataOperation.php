<?php

/**
 * Interface To Define All Data Operations.
 * @author Anindya Gangakhedkar
 *
 */
interface ICSDataOperation
{
	public function getAll($string);
	public function storeAll($beans);
	public function store($bean);
	public function dispenseBeanObject($objectName);
	public function deleteTable($tableName);
	public function getColumn($string);
}
<?php


/**
 * Interface to Specify Methods to Store Data.
 * @author Anindya Gangakhedkar
 *
 */
interface ICSStoreData
{
	 public function storeAllObject($oBeans);
	  public function storeObject($oBean);
	  public function getProductObject();
	  public function getAttributeObject($iNumOfObject = 1);
	  public function getTransactionObject();
	  public function deleteProductTable();
	  public function deleteAttributeTable();
	  public function getProductCount($oPtable, $id);
	  public function createProductTransactionTable($oPtable, $id);
	  public function getTID();
	  public function getattrbibuteIdwithValue();
	  public function getAttributeName($id);
	  public function getArrayMapForAttributes();
	  public function storeMappingTransactionTable($attributeArray);
	  
}
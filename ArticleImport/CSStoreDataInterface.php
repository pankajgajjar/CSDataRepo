<?php
interface CSStoreDataInterface
{
	  public function storeAllObject($oBeans);
	  public function storeObject($oBean);
	  public function getProductObject();
	  public function getAttributeObject( $iNumOfObject = 1 );
	  public function getTransactionObject();
	  public function deleteProductTable();
	  public function deleteAttributeTable();
	  public function getProductCount($oPtable,$id);
	  public function createProductTransactionTable($oPtable,$id);
	  public function getTID();
}
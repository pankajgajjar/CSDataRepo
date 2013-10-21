<?php

require_once 'rb.php';
require_once 'GetCSDataProductForExport.php';
R::setup('mysql:host=localhost;dbname=test','root','');
class StoreCSDataUsingRedBeanPHP {
  
    public function storeAllObject($beans)
    {
       return  R::storeAll($beans);
    }
    
    public function storeObject($bean)
    {
    	return R::store($bean);
    }
    
    function getProductObject()
    {
   		 $ptable = R::dispense('csdataproducts');
    	 return $ptable;
    }
    
    function getAttributeObject()
    {
    	$ctable = R::dispense('csdataattribute');
    	return $ctable;
    }
    
	function getTransactionObject()
    {
    	$ctable = R::dispense('cstransaction');
    	return $ctable;
    }
    
    function deleteProductTable()
    {
    	R::wipe('csdataproducts');
    }
    
    
    
    function deleteAttributeTable()
    {
    	R::wipe('csdataattribute');
    }
    
    function getProductCount($ptable,$id)
    {
    	$totalCount =R::getCol('select count(*) from csdataproducts where tranactionid = '.$id);
    	return $totalCount[0];
    }
    
	
    
    function createProductTransactionTable($ptable,$id)
    {
    	//$ptable = R::dispense('cstransaction');
    	$ptable->date = date('Y-m-d',time());
    	$ptable->insertedrows= self::getProductCount($ptable,$id);
		self::storeObject($ptable);    	
    	return $ptable;
    }
    public function getTID()
    {
    	$totalCount =R::getCol('select MAX(id) from cstransaction');
    	if($totalCount== null)
    	{
    		$totalCount[0]=0;
    	}
    	return $totalCount[0]+1;
    	
    }
    
    

    
    
    
    
}
<?php

require_once 'ormlib/rb.php';
require_once 'CSGetDataProductForExport.php';
require_once 'CSStoreDataInterface.php';
/*
* Configure the database setup
* Define the host name,db name and password
*/
R::setup('mysql:host=localhost;dbname=test','root','');
class CSStoreDataUsingRedBeanPHP implements CSStoreDataInterface {
  
	
	/*
     * Stores a collection of Beans using a bulk store in mySQL
     * @param object $oBean Excepts the Bean 
     * @access public
     */
    public function storeAllObject($oBeans)
    {
       return  R::storeAll($oBeans);
    }

    /*
     * Stores a Beans using RedBean in mySQL
     * @param object $oBean Excepts the Bean 
     * @access public
     */
    public function storeObject($oBean)
    {
    	return R::store($oBean);
    }

    
       /*
     * Return Bean of the Product Table
     * @access public
     */
    public function getProductObject()
    {
   		 $oPtable = R::dispense('csdataproducts');
    	 return $oPtable;
    }
    
       /*
     * Return Bean of the Attribute Table
     * @access public
     */
    
     public function getAttributeObject( $iNumOfObject = 1 )
    {
    	$oCtable = R::dispense('csdataattribute',$iNumOfObject);
    	return $oCtable;
    }
    
    
        /*
     * Return Bean of the Transaction Table
     * @access public
     */
	public function getTransactionObject()
    {
    	$oCtable = R::dispense('cstransaction');
    	return $oCtable;
    }
    
    
        /*
     * Deletes Product Table along with Bean
     * @access public
     */
    public function deleteProductTable()
    {
    	R::wipe('csdataproducts');
    }
    
    
        /*
         * Deletes  Bean Along with the Attribute Table
     * @access public
     */
    public function deleteAttributeTable()
    {
    	R::wipe('csdataattribute');
    }
    
    
      /*
        * Gets Count of Total Products Inserted. 
     	* @access public
     */
   public function getProductCount($oPtable,$id)
    {
    	$iTotalCount =R::getCol('select count(*) from csdataproducts where tranactionid = '.$id);
    	return $iTotalCount[0];
    }
    
	  /*
        * Creates The Transaction Table to Track How many entries were Inserted. 
     	* @access public
     */
    
    public function createProductTransactionTable($oPtable,$id)
    {
    	//$ptable = R::dispense('cstransaction');
    	$oPtable->date = date('Y-m-d',time());
    	$oPtable->insertedrows= self::getProductCount($oPtable,$id);
		self::storeObject($oPtable);    	
    	return $oPtable;
    }
    
      /*
        * Returns Unique Transaction Id to be INserted Into the Transaction Table. 
     	* @access public
     */
    public function getTID()
    {
    	$iTotalCount =R::getCol('select MAX(id) from cstransaction');
    	if($iTotalCount== null)
    	{
    		$iTotalCount[0]=0;
    	}
    	return $iTotalCount[0]+1;
    	
    }
    
    

    
    
    
    
}
<?php

require_once 'ICSStoreData.php';
require_once 'Persistence/CSDataOperations.php';


/**
 * Class to Store Data Using Red Bean Php.
 * @author Anindya Gangakhedkar
 *
 */
class CSStoreDataUsingRedBeanPHP implements ICSStoreData
{

	private $dataOperation;
	
	public function __construct()
	{
		$this->dataOperation = new CSDataOperations();
	}

   /*
   * Stores a collection of Beans using a bulk store in mySQL
   * @param object $oBean Excepts the Bean 
   * @access public
   */
    public function storeAllObject($oBeans)
    {
        return $dataOperation->storeAll($oBeans);
    }

   /*
   * Stores a Beans using RedBean in mySQL
   * @param object $oBean Excepts the Bean 
   * @access public
   */
    public function storeObject($oBean)
    {
        return $dataOperation->store($oBean);
    }


  /*
  * Return Bean of the Product Table
  * @access public
  */
    public function getProductObject()
    {
        $oPtable = $dataOperation->dispenseBeanObject('csdataproducts');
        return $oPtable;
    }

  /*
  * Return Bean of the Attribute Table
  * @access public
  */

    public function getAttributeObject($iNumOfObject = 1)
    {
        $oCtable = $dataOperation->dispenseBeanObject('csdataattribute', $iNumOfObject);
        return $oCtable;
    }


  /*
 * Return Bean of the Transaction Table
 * @access public
 */
    public function getTransactionObject()
    {
        $oCtable =$dataOperation->dispenseBeanObject('cstransaction');
        return $oCtable;
    }


 /*
 * Deletes Product Table along with Bean
 * @access public
 */
    public function deleteProductTable()
    {
        $dataOperation->deleteTable('csdataproducts');
    }


 /*
 * Deletes  Bean Along with the Attribute Table
 * @access public
 */
    public function deleteAttributeTable()
    {
        $dataOperation->deleteTable('csdataattribute');
    }


 /*
 * Gets Count of Total Products Inserted. 
 * @access public
 */
    public function getProductCount($oPtable, $id)
    {
        $iTotalCount = $dataOperation->getColumn('select count(*) from csdataproducts where tranactionid = ' . $id);
        return $iTotalCount[0];
    }

 /*
 * Creates The Transaction Table to Track How many entries were Inserted. 
 * @access public
 */

    public function createProductTransactionTable($oPtable, $id)
    {
        //$ptable = R::dispense('cstransaction');
        $oPtable->date = date('Y-m-d', time());
        $oPtable->insertedrows = self::getProductCount($oPtable, $id);
        self::storeObject($oPtable);
        return $oPtable;
    }

 /*
 * Returns Unique Transaction Id to be INserted Into the Transaction Table. 
 * @access public
 */
    public function getTID()
    {
        $iTotalCount = $dataOperation->getColumn('select MAX(id) from cstransaction');
        if ($iTotalCount == null) {
            $iTotalCount[0] = 0;
        }
        return $iTotalCount[0] + 1;

    }


    public function getattrbibuteIdwithValue()
    {
        $map = $dataOperation->getAll('select distinct attribute_id,attribute_name from csdataattribute');
        return $map;
    }


    public function getAttributeName($id)
    {
        $iTotalCount = $dataOperation->getColumn('select attribute_name from attributemap where attribute_id = ' . $id);
        return $iTotalCount[0];
    }
    
    public function getArrayMapForAttributes()
    {
    	$iTotalMap = $dataOperation->getAll('select distinct conf.PdmarticleconfigurationID attribute_id, conf.Label attribute_name
								FROM'.' '.constant('dbprefix').'Pdmarticleattribute att right outer join'.' '.constant('dbprefix').'Pdmarticleconfiguration conf
								on( conf.PdmarticleconfigurationID = att.PdmarticleconfigurationID )
								where att.VersionNrTo=0');
    	
    	
    	foreach ($iTotalMap as $key => $value)
		{
			$newArray[$value['attribute_id']] = $value['attribute_name'];
		}
    	
    	return $newArray;
    	
    }
    
    public function storeMappingTransactionTable($attributeArray)
    {
    	foreach($attributeArray as $key => $value)
    	{
    		$dataOperation->exec('Insert into cs_transaction_map values('."'".$value."'".","."'".$key."')");
    	}	
    	
    	
    }


}
<?php
include 'CSStoreDataUsingRedBeanPHP.php';
require_once 'CSGetDataProductForExport.php';
require_once 'CSStoreDataInterface.php';

$oCSStoreData = new CSStoreDataUsingRedBeanPHP();

if(!$oCSStoreData instanceof CSStoreDataInterface){
	die("oCSStoreData object is not correctly implemented.");
}

// Deleting attribute and Product Table for test Purposes
$oCSStoreData->deleteProductTable();
$oCSStoreData->deleteAttributeTable();

//Initiating Timer
$iTimer = microtime(true);

// defining array elements 
$oAtable = array();
$oCtable = array();

//getting information about the parent Products
$aParentProducts = CSGetDataProductForExport::getParentProducts();
//alert($aParentProducts[0],1);
$iTotalParentProductsCount = sizeof($aParentProducts);

// Creating the Transaction Table

$oTest = $oCSStoreData->getTransactionObject();
$iTid = $oCSStoreData->getTID();


//Loop for the parent Products
for ($i = 0 ; $i < $iTotalParentProductsCount ; $i ++)
{

	// getting all the Parent Information
	$aProductInformationList = CSGetDataProductForExport::getProductInformationForGivenProduct($aParentProducts[$i]);
	$aAttributeListOfParents = CSGetDataProductForExport::getAllAttributesForGivenProduct($aParentProducts[$i]);
	$aChildrenlist = CSGetDataProductForExport::getChildrenForGivenProduct($aParentProducts[$i]);

	// Creating Product Object for Parent
	$oPtable = $oCSStoreData->getProductObject();
	
			
	// Storing Product Information for the GIven Parent
	$oPtable->productid = $aProductInformationList['ID'];
    $oPtable->label = $aProductInformationList['Label'];
    $oPtable->externalkey = $aProductInformationList['ExternalKey'];
    $oPtable->owner = $aProductInformationList['Owner'];
    $oPtable->tranactionid = $iTid;
    $oPtable->parentid = CSGetDataProductForExport::getParentId($aParentProducts[$i]);
    $oPtable->parentproductname = CSGetDataProductForExport::getParentLabel($aParentProducts[$i]);
    $iProductid = $oCSStoreData->storeObject($oPtable);
	$iParentCounter = 0;
	
	
    // Storing Atrribute INformation for the Given Parent
    foreach($aAttributeListOfParents as $oValue)
    {
                $oAtable[ $iParentCounter] = $oCSStoreData->getAttributeObject();
                $oAtable[ $iParentCounter]->productId = $aProductInformationList['ID'];
				$oAtable[ $iParentCounter]->attributeId = $oValue['attributeId'];
				$oAtable[ $iParentCounter]->attributeName = $oValue['attributeName'];
				$oAtable[ $iParentCounter]->attributeValue = $oValue['attributeValue'];		
                $iParentCounter++;
    }
    
    $iAtributeidForParents = $oCSStoreData->storeAllObject($oAtable);
    
    
    
    // Getting all the Children Information for Product in the same loop.
    $aChildrenlist = CSGetDataProductForExport::getChildrenForGivenProduct($aParentProducts[$i]);
    $iChildrenCounter=0;
     foreach($aChildrenlist as $key=>$value)
     {
     	// Creating Product object for Children.
        $oPtable = $oCSStoreData->getProductObject();
        
        $aChildinformation = CSGetDataProductForExport::getProductInformationForGivenProduct($value);
        $attributeListOfChild = CSGetDataProductForExport::getAllAttributesForGivenProduct($value);
        $oPtable->productid = $aChildinformation['ID'];
        $oPtable->label = $aChildinformation['Label'];
        $oPtable->externalkey = $aChildinformation['ExternalKey'];
        $oPtable->owner = $aChildinformation['Owner'];
 		$oPtable->tranactionid = $iTid;
 		$oPtable->parentid = CSGetDataProductForExport::getParentId($value);
 		$oPtable->parentproductname = CSGetDataProductForExport::getParentLabel($value);        
        $productid = $oCSStoreData->storeObject($oPtable);
    
         //Inserting CHildren Attributes into Attributes Table.
          foreach($attributeListOfChild as $oValue)
            {
                $oCtable[ $iChildrenCounter] = CSStoreDataUsingRedBeanPHP::getAttributeObject();
                $oCtable[ $iChildrenCounter]->productId = $aChildinformation['ID'];
				$oCtable[ $iChildrenCounter]->attributeId = $oValue['attributeId'];
				$oCtable[ $iChildrenCounter]->attributeName = $oValue['attributeName'];
				$oCtable[ $iChildrenCounter]->attributeValue = $oValue['attributeValue'];
                 $iChildrenCounter++;
            }
               $atributeidForChildren = $oCSStoreData->storeAllObject($oCtable);
	}

}

//Inserting Row In Transaction Table
$iTrid =$oCSStoreData->createProductTransactionTable($oTest,$iTid);

//Displaying Total TIme Required in Seconds for Test Purposes.
print_r(microtime(true)-$iTimer.'  '.'sec');

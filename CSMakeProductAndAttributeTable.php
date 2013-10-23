<?php
include 'CSStoreDataUsingRedBeanPHP.php';
require_once 'CSGetDataProductForExport.php';



// Deleting attribute and Product Table for test Purposes
CSStoreDataUsingRedBeanPHP::deleteProductTable();
CSStoreDataUsingRedBeanPHP::deleteAttributeTable();

//Initiating Timer
$iTimer = microtime(true);

// defining array elements 
$oAtable = array();
$oCtable = array();

//getting information about the parent Products
$aParentProducts = CSGetDataProductForExport::getParentProducts();
$iTotalParentProductsCount = sizeof($aParentProducts);



// Creating the Transaction Table
$oTest = CSStoreDataUsingRedBeanPHP::getTransactionObject();
$iTid = CSStoreDataUsingRedBeanPHP::getTID();


	
//Loop for the parent Products
for ($i = 0 ; $i < $iTotalParentProductsCount ; $i ++)
{

	// getting all the Parent Information
	$aProductInformationList = CSGetDataProductForExport::getProductInformationForGivenProduct($aParentProducts[$i]);
	$aAttributeListOfParents = CSGetDataProductForExport::getAllAttributesForGivenProduct($aParentProducts[$i]);
	$aChildrenlist = CSGetDataProductForExport::getChildrenForGivenProduct($aParentProducts[$i]);

	// Creating Product Object for Parent
	$oPtable = CSStoreDataUsingRedBeanPHP::getProductObject();
	
			
	// Storing Product Information for the GIven Parent
	$oPtable->productid = $aProductInformationList['ID'];
    $oPtable->label = $aProductInformationList['Label'];
    $oPtable->externalkey = $aProductInformationList['ExternalKey'];
    $oPtable->owner = $aProductInformationList['Owner'];
    $oPtable->tranactionid = $iTid;
    $oPtable->parentid = CSGetDataProductForExport::getParentId($aParentProducts[$i]);
    $oPtable->parentproductname = CSGetDataProductForExport::getParentLabel($aParentProducts[$i]);
    $iProductid = CSStoreDataUsingRedBeanPHP::storeObject($oPtable);
	$iParentCounter = 0;
	
	
    // Storing Atrribute INformation for the Given Parent
    foreach($aAttributeListOfParents as $oValue)
    {
                $oAtable[ $iParentCounter] = CSStoreDataUsingRedBeanPHP::getAttributeObject();
                $oAtable[ $iParentCounter]->productId = $aProductInformationList['ID'];
				$oAtable[ $iParentCounter]->attributeId = $oValue['attributeId'];
				$oAtable[ $iParentCounter]->attributeName = $oValue['attributeName'];
				$oAtable[ $iParentCounter]->attributeValue = $oValue['attributeValue'];		
                $iParentCounter++;
    }
    $iAtributeidForParents = CSStoreDataUsingRedBeanPHP::storeAllObject($oAtable);
    
    
    
    // Getting all the Children Information for Product in the same loop.
    $aChildrenlist = CSGetDataProductForExport::getChildrenForGivenProduct($aParentProducts[$i]);
    $iChildrenCounter=0;
     for($j =0;$j<sizeof($aChildrenlist);$j++)
     {
     	// Creating Product object for Children.
        $oPtable = CSStoreDataUsingRedBeanPHP::getProductObject();
        
        $aChildinformation = CSGetDataProductForExport::getProductInformationForGivenProduct($aChildrenlist[$j]);
        $attributeListOfChild = CSGetDataProductForExport::getAllAttributesForGivenProduct($aChildrenlist[$j]);
        $oPtable->productid = $aChildinformation['ID'];
        $oPtable->label = $aChildinformation['Label'];
        $oPtable->externalkey = $aChildinformation['ExternalKey'];
        $oPtable->owner = $aChildinformation['Owner'];
 		$oPtable->tranactionid = $iTid;
 		$oPtable->parentid = CSGetDataProductForExport::getParentId($aChildrenlist[$j]);
 		$oPtable->parentproductname = CSGetDataProductForExport::getParentLabel($aChildrenlist[$j]);        
        $productid = CSStoreDataUsingRedBeanPHP::storeObject($oPtable);
    
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
               $atributeidForChildren = CSStoreDataUsingRedBeanPHP::storeAllObject($oCtable);
	}
	
	

}

//Inserting Row In Transaction Table
$iTrid =CSStoreDataUsingRedBeanPHP::createProductTransactionTable($oTest,$iTid);

//Displaying Total TIme Required in Seconds for Test Purposes.
print_r(microtime(true)-$iTimer.'  '.'sec');
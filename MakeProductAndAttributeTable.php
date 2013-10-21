<?php
include 'StoreCSDataUsingRedBeanPHP.php';
require_once 'GetCSDataProductForExport.php';


//Initiating Timer
$timer = microtime(true);

// defining array elements 
$atable = array();
$ctable = array();

//getting information about the parent Products
$parentProducts = GetCSDataProductForExport::getParentProducts();
$totalParentProductsCount = sizeof($parentProducts);


// Deleting attribute and Product Table for test Purposes
StoreCSDataUsingRedBeanPHP::deleteProductTable();
StoreCSDataUsingRedBeanPHP::deleteAttributeTable();

// Creating the Transaction Table
$test = StoreCSDataUsingRedBeanPHP::getTransactionObject();
$tid = StoreCSDataUsingRedBeanPHP::getTID();



//Loop for the parent Products
for ($i = 0 ; $i < $totalParentProductsCount ; $i ++)
{
	// getting all the Parent Information		
	$ptable = StoreCSDataUsingRedBeanPHP::getProductObject();
	$aproductInformationList = GetCSDataProductForExport::getProductInformationForGivenProduct($parentProducts[$i]);
	$attributeListOfParents = GetCSDataProductForExport::getAllAttributesForGivenProduct($parentProducts[$i]);
	$aChildrenlist = GetCSDataProductForExport::getChildrenForGivenProduct($parentProducts[$i]);

	// Storing Product Information for the GIven Parent
	$ptable->productid = $aproductInformationList['ID'];
    $ptable->label = $aproductInformationList['Label'];
    $ptable->externalkey = $aproductInformationList['ExternalKey'];
    $ptable->owner = $aproductInformationList['Owner'];
    $ptable->tranactionid = $tid;
    $ptable->parentid = GetCSDataProductForExport::getParentId($parentProducts[$i]);
    $ptable->parentproductname = GetCSDataProductForExport::getParentLabel($parentProducts[$i]);
    $productid = StoreCSDataUsingRedBeanPHP::storeObject($ptable);
	$parentCounter = 0;
	
	
    // Storing Atrribute INformation for the Given Parent
    foreach($attributeListOfParents as $value)
    {
                $atable[ $parentCounter] = StoreCSDataUsingRedBeanPHP::getAttributeObject();
                $atable[ $parentCounter]->productId = $aproductInformationList['ID'];
				$atable[ $parentCounter]->attributeId = $value['attributeId'];
				$atable[ $parentCounter]->attributeName = $value['attributeName'];
				$atable[ $parentCounter]->attributeValue = $value['attributeValue'];		
               $parentCounter++;
    }
    $atributeidForParents = StoreCSDataUsingRedBeanPHP::storeAllObject($atable);
    
    
    
    // Getting all the Children Information for Product in the same loop.
    $aChildrenlist = GetCSDataProductForExport::getChildrenForGivenProduct($parentProducts[$i]);
    $childrenCounter=0;
     for($j =0;$j<sizeof($aChildrenlist);$j++)
     {
         $ptable = StoreCSDataUsingRedBeanPHP::getProductObject();
         $child_information = GetCSDataProductForExport::getProductInformationForGivenProduct($aChildrenlist[$j]);
          $attributeListOfChild = GetCSDataProductForExport::getAllAttributesForGivenProduct($aChildrenlist[$j]);
         //$productBO->populate($child_information['ID'],$child_information['Owner'],$externalKey,$child_information['Label']);
         $ptable->productid = $child_information['ID'];
         $ptable->label = $child_information['Label'];
         $ptable->externalkey = $child_information['ExternalKey'];
         $ptable->owner = $child_information['Owner'];
 		$ptable->tranactionid = $tid;
 		$ptable->parentid = GetCSDataProductForExport::getParentId($aChildrenlist[$j]);
 		$ptable->parentproductname = GetCSDataProductForExport::getParentLabel($aChildrenlist[$j]);        
         $productid = StoreCSDataUsingRedBeanPHP::storeObject($ptable);
    
         //Inserting CHildren Attributes into Attributes Table.
          foreach($attributeListOfChild as $value)
            {
                
                $ctable[ $childrenCounter] = StoreCSDataUsingRedBeanPHP::getAttributeObject();
                $ctable[ $childrenCounter]->productId = $child_information['ID'];
				$ctable[ $childrenCounter]->attributeId = $value['attributeId'];
				$ctable[ $childrenCounter]->attributeName = $value['attributeName'];
				$ctable[ $childrenCounter]->attributeValue = $value['attributeValue'];
                 $childrenCounter++;
                
            }
               $atributeidForChildren = StoreCSDataUsingRedBeanPHP::storeAllObject($ctable);
	}
	
	

}

//Inserting Row In Transaction Table
$trid =StoreCSDataUsingRedBeanPHP::createProductTransactionTable($test,$tid);

//Displaying Total TIme Required in Seconds for Test Purposes.
print_r(microtime(true)-$timer.'  '.'sec');
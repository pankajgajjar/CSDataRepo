<?php
include 'StoreCSDataUsingRedBeanPHP.php';
//include_once 'Model_ProductBO.php';
require_once 'GetCSDataProductForExport.php';

$parentProducts = GetCSDataProductForExport::getParentProducts();
$totalParentProductsCount = sizeof($parentProducts);
StoreCSDataUsingRedBeanPHP::deleteProductTable();
$timer = microtime(true);
$test = StoreCSDataUsingRedBeanPHP::getTransactionObject();
$tid = StoreCSDataUsingRedBeanPHP::getTID();
print_r($tid);
for ($i = 0 ; $i < $totalParentProductsCount ; $i ++)
{
	// getting all the Parent Information
		
	$ptable = StoreCSDataUsingRedBeanPHP::getProductObject();
	
	$aproductInformationList = GetCSDataProductForExport::getProductInformationForGivenProduct($parentProducts[$i]);
	
	//$aproductInformationList = GetCSDataProductForExport::getProductInformationForGivenProduct($parentProducts[$i]);
    //$productBO->populate($aproductInformationList['ID'],$aproductInformationList['Owner'],$aproductInformationList['ExternalKey'],$aproductInformationList['Label']);
    $ptable->productid = $aproductInformationList['ID'];
    $ptable->label = $aproductInformationList['Label'];
    $ptable->externalkey = $aproductInformationList['ExternalKey'];
    $ptable->owner = $aproductInformationList['Owner'];
    $ptable->tranactionid = $tid;
    $ptable->parentid = GetCSDataProductForExport::getParentId($parentProducts[$i]);
    $ptable->parentproductname = GetCSDataProductForExport::getParentLabel($parentProducts[$i]);
    $productid = StoreCSDataUsingRedBeanPHP::storeObject($ptable);

    
    
    // Getting all the Children Information in the same loop.
    $aChildrenlist = GetCSDataProductForExport::getChildrenForGivenProduct($parentProducts[$i]);
     $child_length = sizeof($aChildrenlist);
     for($j =0;$j<$child_length;$j++)
     {
         $ptable = StoreCSDataUsingRedBeanPHP::getProductObject();
         $child_information = GetCSDataProductForExport::getProductInformationForGivenProduct($aChildrenlist[$j]);
         //$productBO->populate($child_information['ID'],$child_information['Owner'],$externalKey,$child_information['Label']);
         $ptable->productid = $child_information['ID'];
         $ptable->label = $child_information['Label'];
         $ptable->externalkey = $child_information['ExternalKey'];
         $ptable->owner = $child_information['Owner'];
 		$ptable->tranactionid = $tid;
 		$ptable->parentid = GetCSDataProductForExport::getParentId($aChildrenlist[$j]);
 		$ptable->parentproductname = GetCSDataProductForExport::getParentLabel($aChildrenlist[$j]);        
         $productid = StoreCSDataUsingRedBeanPHP::storeObject($ptable);
    
	}

}
$trid =StoreCSDataUsingRedBeanPHP::createProductTransactionTable($test,$tid);

print_r(microtime(true)-$timer.'  '.'sec');
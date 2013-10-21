<?php
include 'StoreCSDataUsingRedBeanPHP.php';
//include 'AttributeBO.php';
require_once 'GetCSDataProductForExport.php';
$atable = array();
$ctable = array();
$parentProducts = GetCSDataProductForExport::getParentProducts();
$parentSize =sizeof($parentProducts); 
StoreCSDataUsingRedBeanPHP::deleteAttributeTable();
$timer= microtime(true);
for($k = 0;$k < $parentSize; $k++){
    $aproductInformationList = GetCSDataProductForExport::getProductInformationForGivenProduct($parentProducts[$k]);
    $attributeListOfParents = GetCSDataProductForExport::getAllAttributesForGivenProduct($parentProducts[$k]);
    $aChildrenlist = GetCSDataProductForExport::getChildrenForGivenProduct($parentProducts[$k]);
    
    $parentCounter = 0;
    
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
    
    $childrenCounter = 0;
    for($i = 0;$i < sizeof($aChildrenlist); $i++ )
    {
    	    $attributeListOfChild = GetCSDataProductForExport::getAllAttributesForGivenProduct($aChildrenlist[$i]);
        	 $aChildInformationList = GetCSDataProductForExport::getProductInformationForGivenProduct($aChildrenlist[$i]);
            foreach($attributeListOfChild as $value)
            {
                
                $ctable[ $childrenCounter] = StoreCSDataUsingRedBeanPHP::getAttributeObject();
                $ctable[ $childrenCounter]->productId = $aChildInformationList['ID'];
				$ctable[ $childrenCounter]->attributeId = $value['attributeId'];
				$ctable[ $childrenCounter]->attributeName = $value['attributeName'];
				$ctable[ $childrenCounter]->attributeValue = $value['attributeValue'];
                 $childrenCounter++;
                
            }
               $atributeidForChildren = StoreCSDataUsingRedBeanPHP::storeAllObject($ctable);
    }
   
 
	
}



print_r(microtime(true)-$timer.'  '.'sec');




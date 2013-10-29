<?php
include 'CSStoreDataUsingRedBeanPHP.php';
require_once 'CSGetDataProductForExport.php';

//initialising timer
$iTimer = microtime(true);
//Getting Parent Products
$oProductList = CSGetDataProductForExport::getParentProducts();
//Deleting all instances of Tables
CSStoreDataUsingRedBeanPHP::deleteProductTable();
CSStoreDataUsingRedBeanPHP::deleteAttributeTable();
//Generating Transaction Table and Transaction Id
$oTest = CSStoreDataUsingRedBeanPHP::getTransactionObject();
$iTid = CSStoreDataUsingRedBeanPHP::getTID();
// Initialising Counters for Bulk Commit
$pcounter = 0;
$acounter = 0;

//Traversing All Products
for ($i = 0; $i < sizeof($oProductList); $i++) {
    $oParentProduct = CSGetDataProductForExport::getValuesofProduct($oProductList[$i]);
    foreach ($oParentProduct as $key => $value) {
        //Inserting Into Product Table
        $oPtable[$pcounter] = CSStoreDataUsingRedBeanPHP::getProductObject();
        $oPtable[$pcounter]->productid = $value['PdmarticlelocaleID'];
        $oPtable[$pcounter]->label = $value['Label'];
        $oPtable[$pcounter]->externalkey = $value['ExternalKey'];
        $oPtable[$pcounter]->parentid = $value['ParentID'];
        $oPtable[$pcounter]->tranactionid = $iTid;
        $pcounter++;

        foreach ($value as $key1 => $value1) {
            //Inserting Into Attribute Table
            if (is_numeric($key1) && $key1 > 0) {
                $oAtable[$acounter] = CSStoreDataUsingRedBeanPHP::getAttributeObject();
                $oAtable[$acounter]->productId = $value['PdmarticlelocaleID'];
                $oAtable[$acounter]->attributeId = $key1;
                $oAtable[$acounter]->attributeName = CSStoreDataUsingRedBeanPHP::getAttributeName($key1);
                $oAtable[$acounter]->attributeValue = $value1;
                $acounter++;
            }
        }

    }
}

$iTrid = CSStoreDataUsingRedBeanPHP::createProductTransactionTable($oTest, $iTid);
$iProductid = CSStoreDataUsingRedBeanPHP::storeAllObject($oPtable);
$iAtributeidForParents = CSStoreDataUsingRedBeanPHP::storeAllObject($oAtable);

//Calculating Total Time
print_r(microtime(true) - $iTimer . '  ' . 'sec');
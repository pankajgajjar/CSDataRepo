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

//getting attribute mapping array

$map = CSStoreDataUsingRedBeanPHP::getArrayMapForAttributes();

//Traversing All Products
for ($i = 0; $i < sizeof($oProductList); $i++) {
    $oParentProduct = CSGetDataProductForExport::getValuesofProduct($oProductList[$i]);
    foreach ($oParentProduct as $key => $value) {
        //Inserting Into Product Table
        $oPtable[$pcounter] = CSStoreDataUsingRedBeanPHP::getProductObject();
        $oPtable[$pcounter]->productid = $value['PdmarticleID'];
        $oPtable[$pcounter]->label = $value['Label'];
        $oPtable[$pcounter]->externalkey = $value['ExternalKey'];
        $oPtable[$pcounter]->parentid = $value['ParentID'];
        $oPtable[$pcounter]->VersionNrFrom = $value['VersionNrFrom'];
        $oPtable[$pcounter]->LanguageID = $value['LanguageID'];
        $oPtable[$pcounter]->VersionNrTo = $value['VersionNrTo'];
        $oPtable[$pcounter]->VersionNote = $value['VersionNote'];
        $oPtable[$pcounter]->LastChange = $value['LastChange'];
        $oPtable[$pcounter]->CreationDate = $value['CreationDate'];
        $oPtable[$pcounter]->LastEditor = $value['LastEditor'];
        $oPtable[$pcounter]->Author = $value['Author'];
        $oPtable[$pcounter]->CheckoutUser = $value['CheckoutUser'];
        $oPtable[$pcounter]->VersionNr = $value['VersionNr'];
        $oPtable[$pcounter]->ParentID = $value['ParentID'];
        $oPtable[$pcounter]->IsFolder = $value['IsFolder'];
        $oPtable[$pcounter]->SortOrder = $value['SortOrder'];
        $oPtable[$pcounter]->PdmarticleconfigurationID = $value['PdmarticleconfigurationID'];
        $oPtable[$pcounter]->WorkflowID = $value['WorkflowID'];
        $oPtable[$pcounter]->StateID = $value['StateID'];
        $oPtable[$pcounter]->tranactionid = $iTid;
        $pcounter++;

        foreach ($value as $key1 => $value1) {
            //Inserting Into Attribute Table
            if (is_numeric($key1) && $key1 > 0) {
                $oAtable[$acounter] = CSStoreDataUsingRedBeanPHP::getAttributeObject();
                $oAtable[$acounter]->productId = $value['PdmarticlelocaleID'];
                $oAtable[$acounter]->attributeId = $key1;
                $oAtable[$acounter]->attributeName = CSGetDataProductForExport::getAttributeName($map,$key1);
                $oAtable[$acounter]->attributeValue = $value1;
                $acounter++;
            }
        }

    }
}

$iProductid = CSStoreDataUsingRedBeanPHP::storeAllObject($oPtable);
$iAtributeidForParents = CSStoreDataUsingRedBeanPHP::storeAllObject($oAtable);


//Calculating Total Time
print_r(microtime(true) - $iTimer . '  ' . 'sec');



$iTrid = CSStoreDataUsingRedBeanPHP::createProductTransactionTable($oTest, $iTid);
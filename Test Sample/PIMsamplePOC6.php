<?php
/**
 * This PIM Sample POC has following functionality
 * Create Product and Attribute type to table
 * Assign Class of Table to this Attribute
 * Assign this attribute to Class 
 * Assign Class to Product
 * Defining the tables values to this attribute for this product 
 */

/**
* Create Product Folder for Storing all the newly created products
*/

$productfolder = CSPms::createProductFolder('ProductFolderTable',null,CSITEM_POSITION_CHILD,0);
$productfolderID = $productfolder->store();
$productfolder->checkin();

echo '<br/>the product folder is created with folder name '.$productfolder->getLabel().' and folder id '.$productfolderID;



/**
* Create one Product under the above defined Product Folder
*/

$product = CSPms::createProduct('Product',$productfolderID);
$productID = $product->store();
$product->checkin();

echo '<br/> the product is created with product name '.$product->getLabel().' and product id '.$productID;


/**
* Create Class for the given Product
*/

$class = CSPms::createClass('TableClass1',null,CSITEM_POSITION_CHILD);
$classID = $class->store();

echo '<br/>the configuration is created with name '.$class->getlabel().' and it configuration id '.$classID;


/**
* Create Attributes to assign to the Given Configuration
*/

$configuration = CSPms::createConfiguration('TableConfiguration1',null,CSITEM_POSITION_CHILD);
$configurationId = $configuration->store();


/**
 *  Creating Attribute under the defined configuration and assign that attribute to given class object 
 */
    $ItemconfigurationID = '223';//this class has limited attribute information:)
	$attribute = CSPms::createField('Attribute_TableType',$configurationId,CSITEM_POSITION_CHILD,'table'); //?articlereference ?
	$attribute->setValue('ItemconfigurationID',$ItemconfigurationID);
	$attributeID = $attribute->store();
	$class->addField($attributeID);
	$class->store();
 
 echo '<br/>the attributes is created with and stored inside the array' ;
 
/**
* Assign the Class or Configuration to Product
*/

$product = CSPms::getProduct($productID);
$product->checkout();
$product->setBaseField($classID);
$product->store();
$product->checkin();

echo '<br/> the product name '.$product->getLabel().' has configuration or class '.$class->getLabel();

/**
 * Setting Table Value to this attribute.
 */
$product = CSPms::getProduct($productID);
$product->checkout();

$valueTable = $product->getTable($attributeID);

/**
 * Get list of attributes for this table class
 */
$itemconfiguration = CSPms::getField($ItemconfigurationID);

//Adding one row for this table
$row1 = $valueTable->addRow();
//Setting values for each attribute of this table class
foreach ($itemconfiguration->getLinkedIDs() as $attriID){
	$row1->setValue($attriID,'India1'.$attriID);
}
//Saving this row 
$row1->store();

//Saving this Product
$product->store();
$product->checkin();

echo '<br/> the test executed sucessfully';

//Delete all records
// 	$attribute->delete();
// 	$configuration->delete();
// 	$class->delete();
// 	$product->delete();
// 	$productfolder->delete();

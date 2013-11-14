<?php
CS::loadApi('CSPms');

/**
 * Create Product Folder for Storing all the newly created products
 */

$productfolder = CSPms::createProductFolder('PankajFolder',null,CSITEM_POSITION_CHILD,0);
$productfolderID = $productfolder->store();
$productfolder->checkin();

echo '<br/>the product folder is created with folder name '.$productfolder->getLabel().' and folder id '.$productfolderID;


/**
 * Create Product under the above Product Folder
 */

$product = CSPms::createProduct('PankajProduct',$productfolderID);
$productID = $product->store();
$product->checkin();

echo '<br/> the product is created with product name '.$product->getLabel().' and product id '.$productID;

/**
 *  Create Class or Configuration
 */

$configuration = CSPms::createConfiguration('PankajConfiguration',null,CSITEM_POSITION_CHILD);
$configurationID = $configuration->store();
//NOTE : Not need for checkin/checkout concept for this configuration object

echo '<br/>the configuration is created with name '.$configuration->getlabel().' and it configuration id '.$configurationID;

/**
 *  Create Attribute 
 */

$attribute = CSPms::createField('PankajAttribute',$configurationID,CSITEM_POSITION_CHILD,'caption');
$attributeID = $attribute->store();
$attribute->checkin(); 

echo '<br/>the attribute is created with name '. $attribute->getLabel().' and it attribute id '.$attributeID;

/**
 *  Assign the Attribute to Class or Configuration
 */

$attribute = CSPms::getApiItem($attributeID);
$attribute->checkout();
$attribute->setValue('ParentID', $configurationID);
$attribute->checkin();

echo '<br/>the attribute name '.$attribute->getLabel().' is assigned to class or configuration '.$configuration->getLabel();
		
/**
 * Assign the Class or Configuration to Product
 */

$product = CSPms::getProduct($productID);
$product->checkout();
$product->setBaseField($configurationID);
$product->checkin();

echo '<br/> the product name '.$product->getLabel().' has configuration or class '.$configuration->getLabel();
/**
 * Assign value for all the attribute of this class for this product
 */
$product = CSPms::getProduct($productID);
$product->checkout();
$product->setValue($attributeID,'PankajValue');
$product->checkin();

echo '<br/>the product name '.$product->getLabel().' has attribute name '.$attribute->getLabel().' and it value is'.$product->getValue($attributeID);
/**
 * Happy Ending ..
 */
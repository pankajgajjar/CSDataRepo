<?php
/**
 * This PIM Sample POC has following functionality
 * Create Product and Attribute Reference to PIM Ref Product
 *  Assign Classs as Attribute Type to Attribute
 *  Create Value Table
 * Assinging it to attribute
 * Assinging attribute to product 
 */

/**
* Create Product Folder for Storing all the newly created products
*/

$productfolder = CSPms::createProductFolder('ProductFolder',null,CSITEM_POSITION_CHILD,0);
$productfolderID = $productfolder->store();
$productfolder->checkin();

echo '<br/>the product folder is created with folder name '.$productfolder->getLabel().' and folder id '.$productfolderID;



/**
* Create one Product under the above defined Product Folder
*/

$product = CSPms::createProduct('Product2',$productfolderID);
$productID = $product->store();
$product->checkin();

echo '<br/> the product is created with product name '.$product->getLabel().' and product id '.$productID;


/**
* Create Class for the given Product
*/

$class = CSPms::createClass('Class2',null,CSITEM_POSITION_CHILD);
$classID = $class->store();

echo '<br/>the configuration is created with name '.$class->getlabel().' and it configuration id '.$classID;


/**
* Create Attributes to assign to the Given Configuration
*/

$configuration = CSPms::createConfiguration('Configuration2',null,CSITEM_POSITION_CHILD);
$configurationId = $configuration->store();


/**
 *  Creating Attribute under the defined configuration and assign that attribute to given class object 
 */
	$attribute = CSPms::createField('Attribute_ProductRefType2',$configurationId,CSITEM_POSITION_CHILD,'articlereference','pdm'); //?articlereference ?
	$attributeID = $attribute->store();
	$class->addField($attributeID);
	$class->store();
 
 echo '<br/>the attributes is created with and stored inside the array' ;
 
 $attribute->setValue('ParamH', 'PdmArticle');
 $attribute->store();
 
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
 * Setting more than one values to this product ref attribute.
 */
$refProduct = array('81','80');
$languageID = 'en';
$additionalArray = array('ParamH'=>'PdmArticle');

$product = CSPms::getProduct($productID);
$product->checkout();
$product->setReferences($attributeID,$refProduct,array(), $languageID,$additionalArray);
$product->store();
$product->checkin();

echo '<br/> the test executed sucessfully';

<?php
/**
 * This PIM Sample POC has following functionality
 * Creating attribute with selectionfield
 * Creating value list thing
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

$product = CSPms::createProduct('Product1',$productfolderID);
$productID = $product->store();
$product->checkin();

echo '<br/> the product is created with product name '.$product->getLabel().' and product id '.$productID;


/**
* Create Class for the given Product
*/

$class = CSPms::createClass('Class1',null,CSITEM_POSITION_CHILD);
$classID = $class->store();

echo '<br/>the configuration is created with name '.$class->getlabel().' and it configuration id '.$classID;


/**
* Create Attributes to assign to the Given Configuration
*/

$configuration = CSPms::createConfiguration('Configuration1',null,CSITEM_POSITION_CHILD);
$configurationId = $configuration->store();


/**
 *  Creating Attribute under the defined configuration and assign that attribute to given class object 
 */
	$attribute = CSPms::createField('Attribute_ValueRange',$configurationId,CSITEM_POSITION_CHILD,'valuerange');
	$attributeID = $attribute->store();
	$class->addField($attribute->getID());
	$class->store();
 
 echo '<br/>the attributes is created with and stored inside the array' ;
 
 /**
  * Creating ValueRang list and define list of values
  */
 /**
  * Creating ValueRang list and define list of values
  */
 $valueRangeType = CSValueRange::createRangeType('PositionValueRange');
 $valueRangeTypeID = $valueRangeType->getID();
 
 
 $attribute->setValue('ParamA', $valueRangeTypeID);
 $attribute->setValue('ParamB', 1);
 $attribute->store();
 
 $values1 = array('en'=>'Front','de'=>'Frontde');
 $values2 = array('en'=>'Rear','de'=>'Rearde');
 $values3 = array('en'=>'Top','de'=>'Topde');
 $values4 = array('en'=>'Bottom','de'=>'Bottomde');

 $valueRange = CSValueRange::createRangeValue($valueRangeTypeID,"",$values1);
 
 $valueRange = CSValueRange::createRangeValue($valueRangeTypeID,"",$values2);
 
 $valueRange = CSValueRange::createRangeValue($valueRangeTypeID,"",$values3);
 
 $valueRange = CSValueRange::createRangeValue($valueRangeTypeID,"",$values4);
 
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
 * Setting more than one values to this attribute.
 */
$product = CSPms::getProduct($productID);
$product->checkout();
// Using setValues Instead of setValue 
//TODO ..insteading value, pass valueRangeID "ID1\nID2\n\ID3"
$product->importValue($attributeID,"Front\nRear\nTop",array());
$product->store();
$product->checkin();

echo '<br/> the test executed sucessfully';
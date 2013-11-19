<?php
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


$attributeID = array();

 for($i=0; $i<2; $i++)
 {
	$attribute = CSPms::createField('Attribute_'.$i,$configurationId,CSITEM_POSITION_CHILD,'caption');
	$attributeID[$i] = $attribute->store();
	$class->addField($attribute->getID());
	$class->store();
 }
 
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
* Assign value for all the attribute of this class for this product
*/

/* Creating the Key Value Array for Testing setValues as per specified format.*/
$keyValue = array();

for($j=0; $j<sizeof($attributeID); $j++)
{
	$keyValue[$attributeID[$j]]= 'Value_'.$j;
}


$product = CSPms::getProduct($productID);
$product->checkout();
// Using setValues Instead of setValue 
$product->setValues($keyValue);
$product->store();
$product->checkin();

echo '<br/> the test executed sucessfully';

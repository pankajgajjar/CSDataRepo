<?php
/**
* Create Product Folder for Storing all the newly created products
*/

$productfolder = CSPms::createProductFolder('AnindyaFolder',null,CSITEM_POSITION_CHILD,0);
$productfolderID = $productfolder->store();
$productfolder->checkin();

echo '<br/>the product folder is created with folder name '.$productfolder->getLabel().' and folder id '.$productfolderID;



/**
* Create one Product under the above defined Product Folder
*/

$product = CSPms::createProduct('AnindyaProduct',$productfolderID);
$productID = $product->store();
$product->checkin();

echo '<br/> the product is created with product name '.$product->getLabel().' and product id '.$productID;


/**
* Create Class or Configuration for the given Product
*/

$configuration = CSPms::createConfiguration('AnindyaConfiguration',null,CSITEM_POSITION_CHILD);
$configurationID = $configuration->store();

echo '<br/>the configuration is created with name '.$configuration->getlabel().' and it configuration id '.$configurationID;


/**
* Create Attributes to assign to the Given Configuration
*/

$attributeID = array();

 for($i=0; $i<2; $i++)
 {
	$attribute = CSPms::createField('AnindyaAttribute'.$i,$configurationID,CSITEM_POSITION_CHILD,'caption');
	$attributeID[$i] = $attribute->store();
 }
 
 echo '<br/>the attributes is created with and stored inside the array' ;
 
 
 
 
 
 
 echo '<br/>.The attributes were assigned to created Configuration';
 
 
 
 /**
* Assign the Class or Configuration to Product
*/

$product = CSPms::getProduct($productID);
$product->checkout();
$product->setBaseField($configurationID);
$product->store();
$product->checkin();

echo '<br/> the product name '.$product->getLabel().' has configuration or class '.$configuration->getLabel();


/**
* Assign value for all the attribute of this class for this product
*/

/* Creating the Key Value Array for Testing setValues as per specified format.*/
$keyValue = array();

for($j=0; $j<sizeof($attributeID); $j++)
{
	$keyValue[$attributeID[$j]]= 'AnindyaValue'.$j;
}


$product = CSPms::getProduct($productID);
$product->checkout();
// Using setValues Instead of setValue 
$product->setValues($keyValue);
$product->store();
$product->checkin();

echo '<br/> the test executed sucessfully';
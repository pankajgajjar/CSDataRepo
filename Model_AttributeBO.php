<?php

Class Model_AttributeBO
{
public $attributeId;
public $attributeValue;
public $attributeName;
public $productId;

public function __construct($attributeId,$attributeValue,$attributeName,$productId)
{
	$this->attributeId = $attributeId;
	$this->attributeName = $attributeName;
	$this->productId = $productId;
	$this->attributeValue = $attributeValue;
		
}

}

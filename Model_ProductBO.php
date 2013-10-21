<?php
class Model_ProductBO
{
	public $productid;
	public $owner;
	public $externalkey;
	public $label;
	
	public function __construct($productid, $owner, $externalkey, $label)
	{
		$this->productid = $productid;
		$this->owner = $owner;
		$this->externalkey = $externalkey;
		$this->label = $label;
	}	
}
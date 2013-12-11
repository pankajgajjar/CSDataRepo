<?php
class  CSDataSchemaFileUtil
{
	private $_aAttributeValueList = array();
	private $_aAttributeExternalValueList = array();
	private $_aEntityObjectList = array();
	private $_aXmlData = NULL;
	private $_sFilePath = NULL;
	
	###############CONSTANT VARIABLE#########
	public static $ATTRIBUTE_CAPTION = 'caption';
	public static $ATTRIBUTE_VALUERANGE = 'valuerange';
	
	
	##############CONSTRUCT##################
	/**
	 * Contructor for ParseDataSchemaFileUtil
	 * 
	 * @param String $sFilePath define the File Path of Data Schema
	 */	
	public function __construct($sFilePath){
		$this->_sFilePath = $sFilePath;
	}
	
	public function parseDataSchemaFile(){
		if(!file_exists($this->_sFilePath)){
			exit("Can't read the Data Schema File ". $this->_sFilePath);
		}
		
		$oXmlFile = simplexml_load_file($this->_sFilePath);
		
		$oAttribute = $oXmlFile->attributes; 
				    
		foreach ($oAttribute->attribute as $childAttr){
			$key = $childAttr["name"];
			$value = $childAttr["type"];
			$externalKey = $childAttr["externalkey"];
			
			$this->_aAttributeValueList[trim($key)] = $value;
			$this->_aAttributeExternalValueList[trim($externalKey)] = $value;
		}
		
		$oEnities = $oXmlFile->entities;
		foreach ($oEnities->entity as $childEntity){
			$this->_aEntityObjectList = $childEntity;
		}
	}
	
	public function getAttributesList(){
		return $this->_aAttributeValueList;
	}
	
	public function getEntitiesList(){
		return $this->_aEntityObjectList;
	}
	
	public function getCSAttributeType($sAttributeLabel){
		return $this->_aAttributeValueList[$sAttributeLabel];
	}
	
	public function isAttributeLabelHasValueList($sAttributeLabel){
		if($this->_aAttributeValueList[$sAttributeLabel] == self::$ATTRIBUTE_VALUERANGE){
			return true;
		}
		return false;
	}
	
	public function isAttributeExternalKeyHasValueList($sExternalKey){
		if($this->_aAttributeExternalValueList[$sExternalKey] == self::$ATTRIBUTE_VALUERANGE){
			return true;
		}
		return false;
	}
	
	public function isAttributeLabelHasCaption($sAttributeLabel){
		if($this->_aAttributeValueList[$sAttributeLabel] == self::$ATTRIBUTE_CAPTION){
			return true;
		}
		return false;
	}
	
	public function isAttributeExternalKeyHasCaption($sExternalKey){
		if($this->_aAttributeExternalValueList[$sExternalKey] == self::$ATTRIBUTE_CAPTION){
			return true;
		}
		return false;
	}
}

########################## TEST CASES #############################
echo "Test Cases Started</br>";
//Data
$sFilePath = "../CSLive/DataSchema.xml";

//Initialize
$oDataSchemaFile = new CSDataSchemaFileUtil($sFilePath);
$oDataSchemaFile->parseDataSchemaFile();

//Validation
assertCSData($oDataSchemaFile->isAttributeLabelHasValueList("FittingPosition"),true);
assertCSData($oDataSchemaFile->isAttributeLabelHasCaption("FittingPosition"),false);

assertCSData($oDataSchemaFile->isAttributeExternalKeyHasValueList("1001"),true);
assertCSData($oDataSchemaFile->isAttributeExternalKeyHasCaption("1001"),false);

function assertCSData($condition,$expectedValue)
{
	if ($condition != $expectedValue) {
		throw new Exception('Test case failed.');
	}
	print "<br/>Test case Passed.";
}

echo "<br/>Test case end";
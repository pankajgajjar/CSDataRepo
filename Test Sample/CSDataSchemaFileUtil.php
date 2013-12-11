<?php
class  CSDataSchemaFileUtil
{
	private $_aAttributeValueList = array();
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
			$this->_aAttributeValueList[trim($key)] = $value;
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
	
	public function isAttributeValueList($sAttributeLabel){
		if($this->_aAttributeValueList[$sAttributeLabel] == self::$ATTRIBUTE_VALUERANGE){
			return true;
		}
		return false;
	}
	
	public function isAttributeCaption($sAttributeLabel){
		if($this->_aAttributeValueList[$sAttributeLabel] == self::$ATTRIBUTE_CAPTION){
			return true;
		}
		return false;
	}
}

echo "Started</br>";
$sFilePath = "../CSLive/DataSchema.xml";

$oDataSchemaFile = new CSDataSchemaFileUtil($sFilePath);
$oDataSchemaFile->parseDataSchemaFile();

//var_dump($oDataSchemaFile->getAttributesList());
//var_dump($oDataSchemaFile->getEntitiesList());
var_dump($oDataSchemaFile->isAttributeValueList("FittingPosition"));
var_dump($oDataSchemaFile->isAttributeCaption("FittingPosition"));
echo "</br>end";
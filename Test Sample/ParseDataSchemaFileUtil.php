<?php
class  ParseDataSchemaFileUtil
{
	private $_aAttributeValueList = array();
	private $_aEntityObjectList = array();
	private $_aXmlData = NULL;
	private $_sFilePath = NULL;
	
	###############CONSTANT VARIABLE#########
	private  static $ATTRIBUTE_CAPTION = 'caption';
	private  static $ATTRIBUTE_VALUERANGE = 'valuerange';
	
	
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
			$_aAttributeValueList[trim($key)] = $value;
		}
		
		$oEnities = $oXmlFile->entities;
		foreach ($oEnities->entity as $childEntity){
			$_aEntityObjectList = $childEntity;
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
		if($this->_aAttributeValueList[$sAttributeLabel] == $ATTRIBUTE_VALUERANGE){
			return true;
		}
		return false;
	}
	
	public function isAttributeCaption($sAttributeLabel){
		if($this->_aAttributeValueList[$sAttributeLabel] == $ATTRIBUTE_CAPTION){
			return true;
		}
		return false;
	}
}

echo "Started";
$sFilePath = "../CSLive/DataSchema.xml";

$oDataSchemaFile = new ParseDataSchemaFileUtil($sFilePath);
$oDataSchemaFile->parseDataSchemaFile();

var_dump($oDataSchemaFile->getAttributesList());
var_dump($oDataSchemaFile->getEntitiesList());
echo "end";
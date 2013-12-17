<?php
require_once 'CSArticleImport.php';
require_once 'CSImportLanguage.php';


//Intializing the Variables
$iTimer=null;
$meyleDataFolderID=null;
$currentClass=null;
$currentClassID=null;
$currentConfigurationID=null;
$languageArrayWithID= null;
$singleValues=null;
$attribute=null;
$valueRanges=null;
$valueRangeType= null;
$valueRangeTypeID=null;
$setValueRangeValues=null;
$articles = null;
$currentProductID = null;
$product= null;
$currentSingleAttributes= null;
$keyValue=null;
$resultforconcat= null;
$output= null;


//Staring Timer To Check Performance
$iTimer = microtime(true);

//Creating The Product Folder
$meyleDataFolderID = CSArticleImport::createProductFolder('Meyle');

//Creating Class and Configuration For the Product And The Attributes
$currentClass = CSArticleImport::createClass('ArticleClass');
$currentClassID = $currentClass->store();
$currentConfigurationID = CSArticleImport::createConfiguration('ArticleConfiguration');

//Getting All languages From Staging Area and Inserting the Missing Languages
$languageArrayWithID = CSImportLanguage::MakeLanguageArrayWithLanguageID(CSArticleImport::getAllLanguagesFromStagingArea());

//Getting All Single Values Attributes
$singleValues = CSArticleImport::getSingleValuesAttributes();

//Traversing the Single Valued Attributes And Storing them In ContentServ
foreach($singleValues as $sValue)
{
	$attribute = CSPms::createField('',$currentConfigurationID,CSITEM_POSITION_CHILD,'caption');
	
	//TODO currently all Attributes are treated as language independent 
	$attribute->setLanguageDependance();
	$attribute->setValue('Label',CSArticleImport::getAttributeLabelwithExternalLanguage('en',$sValue['EXTERNALKEY']));
	$attribute->setValue('ExternalKey',$sValue['EXTERNALKEY']);
	$attributeID[$sValue['EXTERNALKEY']] = $attribute->store();
	
	 //TODO one time store for class
	$currentClass->addField($attribute->getID());
	$currentClass->store();
}


//Getting All Distinct Value Ranges For Article Entity
$valueRanges = CSArticleImport::getAllValueRange();

//Traversing Value Ranges And Creating Them With Set Values In ENGLISH
foreach($valueRanges as $range)
{
	$attribute = CSPms::createField($range['label'],$currentConfigurationID,CSITEM_POSITION_CHILD,'valuerange');
    $attributeID[$range['EXTERNALKEY']] = $attribute->store();
    //TODO one time store for class
    $currentClass->addField($attribute->getID());
    $currentClass->store();
    
    //Creating Value Range Type
    $valueRangeType = CSValueRange::createRangeType($range['label'].'-'.$range['EXTERNALKEY']);
 	$valueRangeTypeID = $valueRangeType->getID();
 	
 	//Setting value as MUlti value List
 	$attribute->setValue('ParamA', $valueRangeTypeID);
 	$attribute->setValue('ParamB', 1);
 	$attribute->store();
 	
 	//Assigning Values To Value Ranges
 	$setValueRangeValues = CSArticleImport::getAllValuesforValueRange($range['EXTERNALKEY'],$valueRangeTypeID);
 		
}

// Getting all the Articles From the Staging Area
$articles = CSArticleImport::getAllArticles();

//Traversing All The Articles And Assigining Attributes to Them.
foreach($articles as $resultArray)
{
	//Creating a Product For Each Article (Language Dependent)
	$currentProductID = CSArticleImport::createProduct($resultArray['Label'],$meyleDataFolderID,$languageArrayWithID,$resultArray['ExternalKey']);
	
	//Looping All Existing Lanaguages to Create Language Dependent Attribute Values
	foreach($languageArrayWithID as $attrLang => $attrLanID)
	{
		//Getting The Current Product And Assigning It To The Master Class
		$product = CSPms::getProduct($currentProductID);
		$product->checkout();
		$product->setBaseField($currentClassID);
		$product->store();
		$product->checkin();
		
		//Getting All Single Valued Attributes For the Respective Product
		//$currentSingleAttributes = null;
		$currentSingleAttributes = CSArticleImport::getSingleValueAttributesforProduct($resultArray['ExternalKey'],$attrLang);
		
		//Checking if The Existing Product Hasd Single Valued Attributes
		if(!empty($currentSingleAttributes))
		{
			
			$keyValue= array();
			//Creating Array Structure to Store AttributeId(ContentServ) VS Attribute Value(Staging Area)		
			foreach($currentSingleAttributes as $value)
			{
					$keyValue[$attributeID[$value['EXTERNALKEY']]] = $value['value'];
				
			}
			
			//Storing Attribute Values For Each Respective Language
			$product->checkout();
			$product->setValues($keyValue,$attrLanID);
		}
		
		//Getting All MutiValued Value Lists For Product
		$currentValueLists = null;
		$currentValueLists = CSArticleImport::getValueListFromAttributeResult($resultArray['ExternalKey']);
		
		//Checking If Value Lists Exists For Current Product With Language English
		if ((!empty($currentValueLists)) && $attrLanID == 1)
		{
				foreach($currentValueLists as $list)
				{
					$resultforconcat = CSArticleImport::getMultiValuedValuesforProduct($resultArray['ExternalKey'],$list['EXTERNALKEY']);
					$output = CSArticleImport::concatValuesWithNewLine($resultforconcat);
					$product->checkout();
					$product->importValue($attributeID[$list['EXTERNALKEY']],$output,array());
				}
		}
			//Storing The Product Attributes And Checking The Product Back into ContentServ	
			$product->store();
			$product->checkin();
	}	
}
//Priting The Total Time Required
print_r(microtime(true)-$iTimer.'  '.'sec');


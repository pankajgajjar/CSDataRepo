<?php

require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Persistence/CSDataOperations.php';
require_once 'ICSArticleImport.php';
/**
 * Class to Facilitate Article Import
 * @author Anindya Gangakhedkar
 *
 */
class CSArticleImport implements ICSArticleImport
{
	
	private $dataOperation;

	public function __construct()
	{
		$this->dataOperation = new CSDataOperations();
	}
	
	/**
  	  * Function Creates Folder Inside Content-Serv
      * @access public
      * @param string $sname name of folder
      * @return product folderID
  	*/
	
	public function createProductFolder($sName)
	{
		$productfolder = CSPms::createProductFolder($sName,null,CSITEM_POSITION_CHILD,0);
		$productfolderID = $productfolder->store();
		$productfolder->checkin();
		return $productfolderID;
		
	}

	
	/**
  	  * Function used to Create A Language Dependent Product
      * @access public
      * @param  $productNameArray array of product names
      * @param $productfolderID the product folder id in contentServ
      * @return productID
  	*/
	
	public function createLangDepedentProduct($productfolderID,$productNameArray)
	{
		$product = CSPms::createProduct("",$productfolderID);
		
		foreach($productNameArray as $productName => $langauge){
			$product->setValue("Label", $productName,$langauge);
		}
		
		$productID = $product->store();
		$product->checkin();
		return $productID;
	}
	
	
	
	/**
  	  * Function Used to Create Language Dependent Prorduct Inside Content-Serv
      * @access public
      * @param  $sProductName name of product
      * @param $productfolderID the product folder id in contentServ
      * @param $externalKey of the Product.
      * @param $language language of the product.
      * @return productID
  	*/
	
	//
	public function createProduct($sProductName,$productfolderID,$language,$externalKey)
	{
		$product = CSPms::createProduct("",$productfolderID);
		
		foreach($language as $key =>$value)
		{
			$product->setValue("Label",self::getUniqueLanguageLabel($externalKey,$key),$value);
			$product->setValue("ExternalKey",$externalKey);
		}
		$productID = $product->store();
		$product->checkin();
		return $productID;
	}
	
	
	/**
  	  * Function to Create COnfiguration Inside Content-Serv
      * @access public
      * @param  $configName name of name of configuration
      * @return $configurationId.
  	*/
	
	public function createConfiguration($configName)
	{
		$configuration = CSPms::createConfiguration($configName.'Config',null,CSITEM_POSITION_CHILD);
		$configurationId = $configuration->store();
		return $configurationId;
	}
	
	/**
  	  * Function Used to Create Attribute Inside ContentServ
      * @access public
      * @param  $attributeName name of attribute
      * @return $attributeID.
  	*/
	//Function Used to Create Attribute Inside ContentServ
	public function createAttribute($attributeName)
	{
		 $attribute = CSPms::createField($attributeName,$configurationId,CSITEM_POSITION_CHILD,'caption');
        $attributeID = $attribute->store();
        $class->addField($attribute->getID());
        $class->store();
        return $attributeID;
		
	}

	/**
  	  * Queries DataBase to Get all Articles From Staging Area with label,ExternalKey,Language
      * @access public
      * @return $articles array of articles.
  	*/
	public function getAllArticles()
	{
		
		$articles = $this->dataOperation->getAll('Select Label,ExternalKey,Language_Code
												 from cs_stg_incr_article 
												 where Language_Code='."'en'" );
				
      return $articles;
		
	}
	
	/**
  	  * Gets all Product Attributes For a given Product with Specified Language
      * @access public
      * @param $productID
      * @param $language language of the product and attribute
      * @return $attributes array of attributes.
  	*/
	public function getProductAttributes($productID,$language)
	{
	
		$attributes = $this->dataOperation->getAll('SELECT a.Label,v.VALUE, a.ExternalKey FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code = v.Language_Code
		    and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
		    and art.EXTERNALKEY='.$productID." "
			.'and a.Language_Code ='."'".$language."'"." "
       		.'and v.Language_Code ='."'".$language."'"." ");
	
			
      return $attributes;
	}
	
	/**
  	  * Creates Class Inside ContentServ
      * @access public
      * @param $classname
      * @return $class class id of the class created.
  	*/
	
	public function createClass($classname)
	{
		$class = CSPms::createClass($classname.'Class',null,CSITEM_POSITION_CHILD);
		
		return $class;
	}
	
	
	/**
  	  * Gets All the Languages Present in the Staging Area
      * @access public
      * @return $result returns array of languages.
  	*/
	public function getAllLanguagesFromStagingArea()
	{
		$result = array();
		$languages = $this->dataOperation->getAll('SELECT DISTINCT language_code FROM cs_stg_incr_article');
		foreach($languages as $value)
		{
			$result[] = $value['language_code']; 
		}
		return  $result;
	}
	
	/**
  	  * Gets Unique Product Label For given ExternalKey and Language
      * @access public
      * @param $language language of product
      * @param $ExternalKey ExternalKey of product
      * @return $label[0] i.e the label of the product.
  	*/
	//
	public function getUniqueLanguageLabel($ExternalKey,$language)
	{
		$label = array();
		$uniqueLabel = $this->dataOperation->getAll("SELECT Label FROM cs_stg_incr_article where Language_Code = '".$language."' and  ExternalKey = '".$ExternalKey."'");
		foreach($uniqueLabel as $value)
		{
			$label[] = $value['Label'];
		}
		return $label[0];
		
		
	}
	
	/**
  	  * Gets all Unique ExternalKeys For Attributes In the Staging Area
      * @access public
      * @return $distinctExternalKey array.
  	*/
	//
	public function getAllDistinctExternalKey()
	{
		$distinctExternalKey= $this->dataOperation->getAll('SELECT  Distinct a.EXTERNALKEY a.LABEL FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		 													 cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		   													 WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    												and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code = v.Language_Code
		   													 and s.ARTCL_STG_ID = art.ARTCL_STG_ID
		    												and a.Language_Code ='."'en'");
		
		
		return $distinctExternalKey;
	}
	
	
	/**
  	  * Gets Attribute Label For ExternalKey and Specified Language
      * @access public
      * @param $language language of attribute
      * @param $externalKey external key of the attribute.
      * @return $$label.
  	*/
	public function getAttributeLabelwithExternalLanguage($language,$externalKey)
	{
		
		$attributeLabel = $this->dataOperation->getAll('SELECT  Distinct a.Label FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		 												 cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		   												 WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		  												  and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code = v.Language_Code
		  												  and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
		   												 and a.EXTERNALKEY ='."'".$externalKey."'"." "
														.'and a.Language_Code='."'".$language."'");
			
			foreach($attributeLabel as $array)
			{
				$label= $array['Label'];
			}
			
			return $label;
			
		
	}
	
	/**
  	  * Gets all the Value Ranges Present in the Staging in the Area
      * @access public
      * @return $valueRange array.
  	*/
	public function getAllValueRange()
	{
		$valueRange = $this->dataOperation->getAll('SELECT a.label, a.EXTERNALKEY FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
 													WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.Language_Code = v.Language_Code
      												 AND a.Language_Code = '."'en'".' '.
													'GROUP BY a.LABEL, a.EXTERNALKEY
													HAVING count(*) > 1');
        return $valueRange;
	}
	
	/**
  	  * Gets All the Values For Value Ranges
      * @access public
      * @return $valueRange array.
  	*/
	public function getAllValuesforValueRange($externalkey,$valueRangeTypeID)
	{
		$range = self::getDistinctValueRangesValues($externalkey);
		foreach($range as $value)
		{
			$temp = array();
			//TODO Current support for English, support for i18n
			$temp['en'] =$value['VALUE'];
			$valueRange = CSValueRange::createRangeValue($valueRangeTypeID,"",$temp); 
		}
		return 1;
	}
	
	/**
  	  * Commented function for further use. Used to Get distinct Value Ranges per Product
      * @access public
      * @param $attributeid
      * @return $range
  	*/
	public function getDistinctValueRangesValues($attributeid)
	{
		
	
		$range = $this->dataOperation->getAll('SELECT distinct v.VALUE FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  										cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    									WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    									and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code = v.Language_Code 
		    									and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
       						 					and a.Language_Code ='."'en'".' '
												.'and a.EXTERNALKEY ='."'".$attributeid."'");
							
		return $range;
	}
	

	/**
  	  * Gets All the Single Value Attributes From the Staging Area
      * @access public
      * @return $single
  	*/
	public function getSingleValuesAttributes()
	{
		$single = $this->dataOperation->getAll(' SELECT a.label, a.EXTERNALKEY FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
 												WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.Language_Code = v.Language_Code
      							 				AND a.Language_Code = '."'en'".' '.
												'GROUP BY a.LABEL, a.EXTERNALKEY
												HAVING count(*) = 1');
        					
        return $single;
        
        					
	}
	

	/**
  	  * Gets Value List From Product Attribute Result
      * @access public
      * @return $single
  	*/
	public function getValueListFromAttributeResult($externalKey)
	{
		
		$resultValueList = $this->dataOperation->getAll(' SELECT a.label,v.value,a.EXTERNALKEY
                					FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  					      		cs_stg_incr_attribute_value v,cs_stg_incr_article art, 
                      					cs_stg_bridge_attributes sb
		    						WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
                					AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    						and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID 
                					and a.Language_Code = v.Language_Code 
		    						and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
        							and a.Language_Code ='."'en'"." ".
        			  				'and art.ExternalKey ='.$externalKey." ".
                					'and a.EXTERNALKEY in (select z.EXTERNALKEY from(SELECT count(value),a.EXTERNALKEY
                                      FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
                                      WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.Language_Code = v.Language_Code
                                      AND a.Language_Code ='."'".'en'."'"." ".
                                      'GROUP BY a.EXTERNALKEY
                                      HAVING count(value) > 1)z)
               						 Group by a.label,v.value,a.EXTERNALKEY');
       	
       									
     	return $resultValueList;
		
	}
	
	//Get All the values for Value Range For A Specific Product
	public function getMultiValuedValuesforProduct($productid,$attributeId){
		$resultForConcat = array();
       									
		$multiValued = $this->dataOperation->getAll(' SELECT  v.VALUE, a.ExternalKey FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  								cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		   								 WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		   								 and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code = v.Language_Code 
		   								 and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
			 							 and a.Language_Code ='."'".'en'."'"." "
       									.'and v.Language_Code ='."'".'en'."'"." "
       									.'and art.EXTERNALKEY='.$productid." ".
       									'and a.ExternalKey='."'".$attributeId."'");
       									
        foreach ($multiValued as $value){
        	$resultForConcat[] = $value['VALUE'];
        }
        return $resultForConcat;
		
	}
	
	/**
  	  * Concats Values to New Line
      * @access public
      * @param $array to concat
      * @return $output string
  	*/
	public function concatValuesWithNewLine($array)
	{
		$output = null;
		
		foreach($array as $value)
		{
			$output= $output . $value . PHP_EOL;
			 
		}
		$output =  substr($output, 0, -2);
		
		return $output;
	}
	
	/**
  	  * Get All Single Valued Attributes for Product Attribute Result
      * @access public
      * @param $externalKey of attribute
      * @param $language of sttribute
      * @return $singleValue 
  	*/
	public function getSingleValueAttributesforProduct($externalKey,$language)
	{
		
		$singleValue = $this->dataOperation->getAll(' SELECT a.label,v.value,a.EXTERNALKEY
                					FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  					      		cs_stg_incr_attribute_value v,cs_stg_incr_article art, 
                      					cs_stg_bridge_attributes sb
		    						WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
                					AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    						and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID 
                					and a.Language_Code= v.Language_Code
		    						and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
        							and a.Language_Code='."'".$language."'"." ".
        			  				'and art.ExternalKey ='.$externalKey." ".
                					'and a.EXTERNALKEY in (select z.EXTERNALKEY from(SELECT count(value),a.EXTERNALKEY
                                      FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
                                      WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.Language_Code= v.Language_Code
                                      AND a.Language_Code='."'".'en'."'"." ".
                                      'GROUP BY a.EXTERNALKEY
                                      HAVING count(value) = 1)z)
               						 Group by a.label,v.value,a.EXTERNALKEY');
        					
        return $singleValue;
		
	}
	
	
	/**
	 * Stores Single Valued Attributes
	 * @param  $currentConfigurationID configuration id of the current configuration
	 * @param  $currentClass The current class in use
	 * @param  $singleValues The bean object of all single values
	 * @return $attributeID array
	 */
	public function storeSingleValuedAttributes($currentConfigurationID,$currentClass,$singleValues)
	{
		foreach($singleValues as $sValue)
		{
			$attribute = CSPms::createField('',$currentConfigurationID,CSITEM_POSITION_CHILD,'caption');
			$attribute->setLanguageDependance();
			$attribute->setValue('Label',self::getAttributeLabelwithExternalLanguage('en',$sValue['EXTERNALKEY']));
			$attribute->setValue('ExternalKey',$sValue['EXTERNALKEY']);
			$attributeID[$sValue['EXTERNALKEY']] = $attribute->store();

			$currentClass->addField($attribute->getID());
			$currentClass->store();
		}		
		return $attributeID;
	}
	
	
	/**
	 * Stores the value ranges in the staging area insides contentserv
	 * @param $currentConfigurationID contains the current configuration id
	 * @param $currentClass the current class is use
	 * @param $valueRanges bean object of all value ranges
	 * @param $attributeID The array of attribute ids with mapping in contentserv
	 * @return Ambigous <number, void, boolean, unknown>
	 */
	public function storeValueRanges($currentConfigurationID,$currentClass,$valueRanges,$attributeID)
	{
		foreach($valueRanges as $range)
		{
			$attribute = CSPms::createField($range['label'],$currentConfigurationID,CSITEM_POSITION_CHILD,'valuerange');
			$attributeID[$range['EXTERNALKEY']] = $attribute->store();
			//TODO one time store for class
			$currentClass->addField($attribute->getID());
			$currentClass->store();

			$valueRangeType = CSValueRange::createRangeType($range['label'].'-'.$range['EXTERNALKEY']);
			$valueRangeTypeID = $valueRangeType->getID();

			$attribute->setValue('ParamA', $valueRangeTypeID);
			$attribute->setValue('ParamB', 1);
			$attribute->store();
			$setValueRangeValues = self::getAllValuesforValueRange($range['EXTERNALKEY'],$valueRangeTypeID);				
		}
		
		return $attributeID;
	}

	
	/**
	 * Creates Product inside ContentServ and sets values for its attributes
	 * @param  $attributeID contains attribute id with contentserv id mapping in array form
	 * @param  $currentClassID contains the id of the current class
	 * @param  $articles contains bean object of all articles inside the staging area
	 * @param  $languageArrayWithID contains all the languages in contentserv along with shortname as their index
	 * @param  $meyleDataFolderID contains the current meyle data folder id
	 */
	public function createProductAndAssignValues($attributeID,$currentClassID,$articles,$languageArrayWithID,$meyleDataFolderID)
	{
		foreach($articles as $resultArray)
		{
			$currentProductID = self::createProduct($resultArray['Label'],$meyleDataFolderID,$languageArrayWithID,$resultArray['ExternalKey']);
			foreach($languageArrayWithID as $attrLang => $attrLanID)
			{
				$product = CSPms::getProduct($currentProductID);
				$product->checkout();
				$product->setBaseField($currentClassID);
				$product->store();
				$product->checkin();

				$currentSingleAttributes = null;
				$currentSingleAttributes = self::getSingleValueAttributesforProduct($resultArray['ExternalKey'],$attrLang);

				if(!empty($currentSingleAttributes))
				{						
					$keyValue= array();
					foreach($currentSingleAttributes as $value)
					{
						$keyValue[$attributeID[$value['EXTERNALKEY']]] = $value['value'];
					}						
					$product->checkout();
					$product->setValues($keyValue,$attrLanID);
				}
				$currentValueLists = null;
				$currentValueLists = self::getValueListFromAttributeResult($resultArray['ExternalKey']);

				if ((!empty($currentValueLists)) && $attrLanID == 1)
				{
					foreach($currentValueLists as $list)
					{
						$resultforconcat = self::getMultiValuedValuesforProduct($resultArray['ExternalKey'],$list['EXTERNALKEY']);
						$output = self::concatValuesWithNewLine($resultforconcat);
						$product->checkout();
						$product->importValue($attributeID[$list['EXTERNALKEY']],$output,array());
					}
				}
				$product->store();
				$product->checkin();
			}
		}
		
	}
	
	
}
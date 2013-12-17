<?php

require_once 'rb.php';
require_once 'CSStoreDataUsingRedBeanPHP.php';

class CSArticleImport
{
	
	//Function Creates Folder Inside Content-Serv
	public function createProductFolder($sName)
	{
		$productfolder = CSPms::createProductFolder($sName,null,CSITEM_POSITION_CHILD,0);
		$productfolderID = $productfolder->store();
		$productfolder->checkin();
		return $productfolderID;
	}
	
	//Function used to Create A Language Dependent Product
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
	
	
	//Function Used to Create Language Dependent Prorduct Inside Content-Serv
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
	
	
	//Function to Create COnfiguration Inside Content-Serv
	public function createConfiguration($configName)
	{
		$configuration = CSPms::createConfiguration($configName.'Config',null,CSITEM_POSITION_CHILD);
		$configurationId = $configuration->store();
		return $configurationId;
	}
	

	//Function Used to Create Attribute Inside ContentServ
	public function createAttribute($attributeName)
	{
		 $attribute = CSPms::createField($attributeName,$configurationId,CSITEM_POSITION_CHILD,'caption');
        $attributeID = $attribute->store();
        $class->addField($attribute->getID());
        $class->store();
        return $attributeID;
		
	}
	
	//Queries DataBase to Get all Articles From Staging Area with label,ExternalKey,Language
	public function getAllArticles()
	{
		
		$temp = array();
		$articles = R::getAll('Select Label,ExternalKey,Language from cs_stg_incr_article where Language='."'en'" );
				
      return $articles;
		
	}
	
	//Gets all Product Attributes For a given Product with Specified Language
	public function getProductAttributes($productID,$language)
	{
		$array = array();
	
		$attributes = R::getAll('SELECT a.Label,v.VALUE, a.ExternalKey FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.LANGUAGE = v.LANGUAGE 
		    and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
		    and art.EXTERNALKEY='.$productID." "
			.'and a.Language ='."'".$language."'"." "
       		.'and v.LANGUAGE ='."'".$language."'"." ");
	
			
      return $attributes;
	}
	
	//Creates Class Inside ContentServ
	public function createClass($classname)
	{
		$class = CSPms::createClass($classname.'Class',null,CSITEM_POSITION_CHILD);
		return $class;
	}
	
	//Gets All the Languages Present in the Staging Area
	public function getAllLanguagesFromStagingArea()
	{
		$result = array();
		$languages = R::getAll('SELECT DISTINCT language FROM cs_stg_incr_article');
		foreach($languages as $value)
		{
			$result[] = $value['language']; 
		}
		return  $result;
	}
	
	//Gets Unique Product Label For given ExternalKey and Language
	public function getUniqueLanguageLabel($ExternalKey,$language)
	{
		$label = array();
		$uniqueLabel = R::getAll("SELECT Label FROM cs_stg_incr_article where Language = '".$language."' and  ExternalKey = '".$ExternalKey."'");
		foreach($uniqueLabel as $value)
		{
			$label[] = $value['Label'];
		}
		return $label[0];
		
		
	}
	
	//Gets all Unique ExternalKeys For Attributes In the Satging Area
	public function getAllDistinctExternalKey()
	{
		$distinctExternalKey= R::getAll('SELECT  Distinct a.EXTERNALKEY a.LABEL FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.LANGUAGE = v.LANGUAGE 
		    and s.ARTCL_STG_ID = art.ARTCL_STG_ID
		    and a.Language ='."'en'");
		
		
		return $distinctExternalKey;
	}
	
	//Gets Attribute Label For ExternalKey and Specified Language
	public function getAttributeLabelwithExternalLanguage($language,$externalKey)
	{
		
		$attributeLabel = R::getAll('SELECT  Distinct a.Label FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.LANGUAGE = v.LANGUAGE 
		    and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
		    and a.EXTERNALKEY ='."'".$externalKey."'"." "
			.'and a.Language='."'".$language."'");
			
			foreach($attributeLabel as $array)
			{
				$label= $array['Label'];
			}
			
			return $label;
			
		
	}
	
	// Gets all the Value Ranges Present in the Staging in the Area
	public function getAllValueRange()
	{
		$valueRange = R::getAll('SELECT a.label, a.EXTERNALKEY FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
 								WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.LANGUAGE = v.LANGUAGE
      							 AND a.LANGUAGE = '."'en'".' '.
								'GROUP BY a.LABEL, a.EXTERNALKEY
								HAVING count(*) > 1');
        return $valueRange;
	}
	
	
	//Gets All the Values For Value Ranges
	public function getAllValuesforValueRange($externalkey,$valueRangeTypeID)
	{
		$result = array();
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
	
	// Commented function for further use. Used to Get distinct Value Ranges per Product
	public function getDistinctValueRangesValues($attributeid)
	{
		
	
		$range = R::getAll('SELECT distinct v.VALUE FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  					cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    				WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    				and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.LANGUAGE = v.LANGUAGE 
		    				and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
       						 and a.LANGUAGE ='."'en'".' '
							.'and a.EXTERNALKEY ='."'".$attributeid."'");
							
		return $range;
	}
	

	//Gets All the Single Value Attributes From the Staging Area
	public function getSingleValuesAttributes()
	{
		$single = R::getAll(' SELECT a.label, a.EXTERNALKEY FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
 								WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.LANGUAGE = v.LANGUAGE
      							 AND a.LANGUAGE = '."'en'".' '.
								'GROUP BY a.LABEL, a.EXTERNALKEY
								HAVING count(*) = 1');
        					
        return $single;
        
        					
	}
	
	
	//Gets Value List From Product Attribute Result
	public function getValueListFromAttributeResult($externalKey)
	{
		
		$resultValueList = R::getAll(' SELECT a.label,v.value,a.EXTERNALKEY
                					FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  					      		cs_stg_incr_attribute_value v,cs_stg_incr_article art, 
                      					cs_stg_bridge_attributes sb
		    						WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
                					AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    						and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID 
                					and a.LANGUAGE = v.LANGUAGE 
		    						and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
        							and a.LANGUAGE ='."'en'"." ".
        			  				'and art.ExternalKey ='.$externalKey." ".
                					'and a.EXTERNALKEY in (select z.EXTERNALKEY from(SELECT count(value),a.EXTERNALKEY
                                      FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
                                      WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.LANGUAGE = v.LANGUAGE
                                      AND a.LANGUAGE ='."'".'en'."'"." ".
                                      'GROUP BY a.EXTERNALKEY
                                      HAVING count(value) > 1)z)
               						 Group by a.label,v.value,a.EXTERNALKEY');
       	
       									
     	return $resultValueList;
		
	}
	
	//Get All the values for Value Range For A Specific Product
	public function getMultiValuedValuesforProduct($productid,$attributeId){
		$resultForConcat = array();
       									
		$multiValued = R::getAll(' SELECT  v.VALUE, a.ExternalKey FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  								cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		   								 WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		   								 and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.LANGUAGE = v.LANGUAGE 
		   								 and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
			 							 and a.Language ='."'".'en'."'"." "
       									.'and v.LANGUAGE ='."'".'en'."'"." "
       									.'and art.EXTERNALKEY='.$productid." ".
       									'and a.ExternalKey='."'".$attributeId."'");
       									
        foreach ($multiValued as $value){
        	$resultForConcat[] = $value['VALUE'];
        }
        return $resultForConcat;
		
	}
	
	
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
	
	// Get All Single Valued Attributes for Product Attribute Result
	public function getSingleValueAttributesforProduct($externalKey,$language)
	{
		
		$singleValue = R::getAll(' SELECT a.label,v.value,a.EXTERNALKEY
                					FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		  					      		cs_stg_incr_attribute_value v,cs_stg_incr_article art, 
                      					cs_stg_bridge_attributes sb
		    						WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
                					AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    						and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID 
                					and a.LANGUAGE = v.LANGUAGE 
		    						and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
        							and a.LANGUAGE ='."'".$language."'"." ".
        			  				'and art.ExternalKey ='.$externalKey." ".
                					'and a.EXTERNALKEY in (select z.EXTERNALKEY from(SELECT count(value),a.EXTERNALKEY
                                      FROM cs_stg_incr_attribute a,cs_stg_incr_attribute_value v,cs_stg_bridge_attributes sb
                                      WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID AND a.LANGUAGE = v.LANGUAGE
                                      AND a.LANGUAGE ='."'".'en'."'"." ".
                                      'GROUP BY a.EXTERNALKEY
                                      HAVING count(value) = 1)z)
               						 Group by a.label,v.value,a.EXTERNALKEY');
        					
        return $singleValue;
		
	}
	

	
}
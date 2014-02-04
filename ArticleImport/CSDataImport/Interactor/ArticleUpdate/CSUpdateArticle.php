<?php
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Persistence/CSDataOperations.php';
require_once 'ICSUpdateArticle.php';

/**
 * Class Provides Facilities to update from the stagiing Area.
 * @author Anindya Gangakhedkar
 *
 */
class CSUpdateArticle implements ICSUpdateArticle
{
	
	private $dataOperations;
	private $importLanguage;
	
	public function __construct()
	{
		$this->dataOperations = new CSDataOperations();
		$this->importLanguage = new CSImportLanguage();
	}

	
	/**
	 * Returns the Maximum of Transaction ID for the Article Table
	 * @return return maximum of count
	 */
	public function getMaxTransCountForArticle()
	{
		$count = $this->dataOperations->getColumn('Select max(trans_id) from cs_stg_incr_article');

		
		return $count[0];
	}
	
	
	
	/**
	 * Get current PHP process count
	 * @return number
	 */
	public function getCurrentPHPProcessCountforProduct()
	{
		//Pending Implementation on the Database hence returning arbitrary Value
		
		return 8+1;
	}
	
	
	public function getPHPProcessCountForUpperBridge()
	{
		//Database Implementation Pending
		return 6;
	}
	
	
	public function getMaxTransIdForUpperBridge()
	{
		$transID = $this->dataOperations->getColumn('SELECT max(trans_id) from cs_stg_bridge_artcl_attribute');
		return $transID;
	}
	
	
	
	public function getAllAttributeTransIDforUpperBridge($start)
	{
		$transID = $this->dataOperations->getColumn('select Trans_ID FROM cs_stg_data_transaction_tracker where TABLE_NAME =' ."'".'cs_stg_bridge_artcl_attribute'."'". 'and TRANS_ID >'.$start );
		return $transID;
	}
	
	

	
	
	/* Gets Grouped Products For updation From Staging Area
	 * @param $start Trans_id and $end $trans_ID
	 */
	public function getGroupedProductsForUpdation($start,$end)
	{
		$aGroupedProducts =  $this->dataOperations->getAll('select ExternalKey from cs_stg_incr_article where Trans_id Between '.
															$start.' and '.$end.' Group By ExternalKey');
		
		return $aGroupedProducts;
	}
	

	/* Updates Products In a Group by Querying the Staging Area
	 * @param $start Trans_id and $end $trans_ID
	 */
	public function updateProductsGrouped($iStart,$iEnd)
	{
		$languaArray = $this->importLanguage->CheckAvailableLanguages();
		$aProducts = self::getGroupedProductsForUpdation($iStart, $iEnd);
		foreach ($aProducts as $oProduct)
		{
			$product = CSPms::getProductForExternalKey($oProduct['ExternalKey']);
			alert($product->getID());
			if($product->getID() == 0)
			{
				$product = self::createProduct(1147, $languaArray , $oProduct['ExternalKey'], $iStart, $iEnd);
				
			}
			else
			{
				self::updateAllLabelsForExistingProduct($oProduct['ExternalKey'],$product,$iStart,$iEnd,$languaArray);
			}

			$product ->store();
			$product ->checkin();
		}
	}

	
		/* Gets All Updates For A existing Product
		 * @param $start Trans_id and $end $trans_ID
		 * @param $iExternalKey is the ExternalKey of the Product
		 */
		public function getAllUpdatesForExistingProduct($iExternalKey,$start,$end)
		{
			$updatedProducts = $this->dataOperations->getAll('Select Label,Language_Code From cs_stg_incr_article where ExternalKey ='."'".$iExternalKey."'".
															' and Trans_Id between '.$start.' and '.$end);
			return  $updatedProducts;
		}
		
		
		/* Updates Labels For All Existing Products for the Specified Language
		 * @param $iStart Trans_id and $iEnd $trans_ID
		 * @param $iExternalKey is the ExternalKey of the Product
		 * @param $aLanguageArray Array of all the languages
		 */
		public function updateAllLabelsForExistingProduct($iExternalKey,$oProduct,$iStart,$iEnd,$aLanguageArray)
		{
			$aUpdatedExistingProducts = self::getAllUpdatesForExistingProduct($iExternalKey, $iStart, $iEnd);
			
			foreach ($aUpdatedExistingProducts as $product)
			{
				$oProduct->setValue('Label', $product['Label'],$aLanguageArray[$product['Language_Code']]);
			}
			
		
			
		}
		
		
		public function UpdateAllLanguagesInContentServ()
		{
			$languages = $this->dataOperations->getAll('Select Language_Code,Language From cs_stg_language');
			foreach ($languages as $language)
			{
				$langId = $this->importLanguage->checkIfLanguageExists($language['Language_Code']);
				
			}
		}
		
	
	/**
	 * Creates Product with Specified STart and End Trans_id
	 * @param  $sProductName name of the Product
	 * @param  $productfolderID the Id of the current Product folder
	 * @param  $language array of all languages
	 * @param  $externalKey external key of the new Product
	 * @param  $start trans_id
	 * @param  $end trans_id
	 * @return $product object (CSPms)
	 */
	public function createProduct($productfolderID,$language,$externalKey,$start,$end)
	{
		$product = CSPms::createProduct("",$productfolderID);
			
		foreach($language as $key =>$value)
		{
			$product->setValue("Label",self::getUniqueLanguageLabel($externalKey,$key,$start,$end),$value);
			$product->setValue("ExternalKey",$externalKey);
		}
			
		return $product;
	}
	
	
	
		
	
	/**
	 * Get Unique Labels between the specified transaction IDS 
	 * @param  $ExternalKey
	 * @param  $language array of languages(refer CSImportLanguages)
	 * @param  $start trans_id
	 * @param  $end trans_id
	 * @return $returns Label;
	 */
	public function getUniqueLanguageLabel($ExternalKey,$language,$start,$end)
	{
		$label = array();
		$uniqueLabel = $this->dataOperations->getAll("SELECT Label FROM cs_stg_incr_article where Language_Code = '".$language."' and  ExternalKey = '".$ExternalKey."'".' and trans_id between '.$start.' and '.$end);
		foreach($uniqueLabel as $value)
		{
			$label[] = $value['Label'];
		}
		return $label[0];
		
		
	}
	


	/**
	 * Gets ContentServ Id From the Dummy Table
	 * @param  $externalkey of the attribute
	 * @return unknown
	 */
	public function getCSIDForAttribute($externalkey)
	{
		$updateID = $this->dataOperations->getAll('Select CS_ATTR_KEY 
													from cs_transaction_map 
													where ATTR_EXTERNALKEY='."'".$externalkey."'");
		
		if($updateID == null)
		{
			$id = null;
		}
		else {

			foreach($updateID as $value)
			{
				$id = $value['CS_ATTR_KEY'];
			}
		}
		return $id;
	}
	
	
	/**
	 * Gets All the updates Attribute Labels
	 * @return $updatedAttributes return array of updated labels with attribute
	 */
	public function getAllUpdateAttributeLabels()
	{
		$updatedAttributes = $this->dataOperations->getAll('Select Label,EXTERNALKEY From cs_stg_incr_attribute where IS_DELETED='."'U'"." ".
										'and
										TRANS_ID=(select MAX(TRANS_ID) from cs_stg_incr_attribute)');
		return $updatedAttributes;
		
	}
	
	
	
			
	
	public function updateAttributeLabels($updatedAttributeLabels)
	{
		foreach ($updatedAttributeLabels as $attrLabel)
		{
			//alert($updatedArticleLabels);
			$CSid = self::getCSIDForAttribute($attrLabel['EXTERNALKEY']);
			$attribute = CSPms::getField($CSid);
			$attribute->setValue('Label',$attrLabel['Label']);
			$attribute->store();
		}
	}
	
	
	
	
	public function getAllAttributeValuesForGivenTransId($transID)
	{
		$updateValues = $this->dataOperations->getAll('SELECT art.EXTERNALKEY ARTCL_EXTERNALKEY,a.LANGUAGE_CODE,a.EXTERNALKEY ATTR_EXTERNALKEY,a.LABEL ATTR_LABEL,v.VALUE,art.IS_DELETED ARTCL_DELETED
 														 ,a.IS_DELETED ATTR_DELETED,s1.IS_DELETED ARTCL_ATTR__VALUE_DELETED
 														 FROM cs_stg_bridge_artcl_attribute s1, 
      													 cs_stg_incr_attribute a,
													       CS_STG_INCR_ATTRIBUTE_VALUE v,
													       cs_stg_incr_article art,
													       cs_stg_bridge_attributes sb
													 		WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
													       AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID
													       and s1.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID
													       and a.LANGUAGE_CODE = v.LANGUAGE_CODE
													       and s1.ARTCL_STG_ID = art.ARTCL_STG_ID
													       and s1.trans_id='.$transID);
		
		alert($updateValues);
		return $updateValues;
	}
	
	
	public function updateAttributeValues($start)
	{
		
		$transId = self::getAllAttributeTransIDforUpperBridge($start);

		foreach ($transId as $tID)
		{
			$updatedValues = self::getAllAttributeValuesForGivenTransId($tID);

			if($updatedValues != null )
			{
				foreach ($updatedValues as $value)
				{
					$product = CSPms::getProductForExternalKey($value['ARTCL_EXTERNALKEY']);
					if($product->getID()!= 0)
					{
						//TODO : Lookup from contentServ
						$id = self::getCSIDForAttribute($value['ATTR_EXTERNALKEY']);

						if(($id == null) && ($value['ARTCL_ATTR__VALUE_DELETED'] != 'Y'))
						{
							$newAttributeID = self::createNewAttributeForCurrentTransID($value['ATTR_LABEL'], $value['ATTR_EXTERNALKEY'], $product);

							self::storeNewAttributeIdstoTransactionMap($newAttributeID,$value['ATTR_EXTERNALKEY']);

							$product->setValue($newAttributeID, $value['VALUE'],$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));

							$product->store();
						}
						elseif (($id != null) && ($value['ARTCL_ATTR__VALUE_DELETED'] == 'Y'))
						{
							$product->setValue($id,'',$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));
							$product->store();
						}
						else
						{
							$product->setValue($id, $value['VALUE'],$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));
							$product->store();
						}
					}
					$product->checkin();
				}
			}
		}

}



public function updateProductAttributesGrouped($start)
{

	$transId = self::getAllAttributeTransIDforUpperBridge($start);
	foreach ($transId as $tID)
	{
		$updatedProductValues = self::getUpdatedAttributesGrouped($tID);
		foreach($updatedProductValues as $productExternalKey)
		{
			$product = CSPms::getProductForExternalKey($value['ARTCL_EXTERNALKEY']);
			if($product->getID() !=0)
			{
				$values = self::getUpdatedAtrributeValuesForGivenTransIDExternalKey($tID,$value['ARTCL_EXTERNALKEY']);
				foreach($values as $value)
				{
					$id = self::getCSIDForAttribute($value['ATTR_EXTERNALKEY']);
					if(($id == null) && ($value['ARTCL_ATTR__VALUE_DELETED'] != 'Y'))
					{
						$newAttributeID = self::createNewAttributeForCurrentTransID($value['ATTR_LABEL'], $value['ATTR_EXTERNALKEY'], $product);
						self::storeNewAttributeIdstoTransactionMap($newAttributeID,$value['ATTR_EXTERNALKEY']);
						$product->setValue($newAttributeID, $value['VALUE'],$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));
						
					}
					elseif (($id != null) && ($value['ARTCL_ATTR__VALUE_DELETED'] == 'Y'))
					{
						$product->setValue($id,'',$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));
					}
					else
					{
						$product->setValue($id, $value['VALUE'],$this->importLanguage->checkIfLanguageExists($value['LANGUAGE_CODE']));
					}
					
				}
				$product->store();
				$product->checkin();	
			}
		}
	}

}



public function getUpdatedAtrributeValuesForGivenTransIDExternalKey($transID,$externalKey)
{
	$values = $this->dataOperations->getAll('SELECT a.LANGUAGE_CODE,a.EXTERNALKEY ATTR_EXTERNALKEY,a.LABEL ATTR_LABEL,v.VALUE,art.IS_DELETED ARTCL_DELETED
 														 ,a.IS_DELETED ATTR_DELETED,s1.IS_DELETED ARTCL_ATTR__VALUE_DELETED
 														 FROM cs_stg_bridge_artcl_attribute s1, 
      													 cs_stg_incr_attribute a,
													       CS_STG_INCR_ATTRIBUTE_VALUE v,
													       cs_stg_incr_article art,
													       cs_stg_bridge_attributes sb
													 		WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
													       AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID
													       and s1.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID
													       and a.LANGUAGE_CODE = v.LANGUAGE_CODE
													       and s1.ARTCL_STG_ID = art.ARTCL_STG_ID
													       and s1.trans_id='.$transID.
															' and art.EXTERNALKEY='."'".$externalKey."'");
	return $values;
}





public function getUpdatedAttributesGrouped($transID)
{
	$groupedExternalKeys = $this->dataOperations->getAll('SELECT Distinct art.EXTERNALKEY ARTCL_EXTERNALKEY
 														 FROM cs_stg_bridge_artcl_attribute s1, 
      													 cs_stg_incr_attribute a,
													       CS_STG_INCR_ATTRIBUTE_VALUE v,
													       cs_stg_incr_article art,
													       cs_stg_bridge_attributes sb
													 		WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID
													       AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID
													       and s1.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID
													       and a.LANGUAGE_CODE = v.LANGUAGE_CODE
													       and s1.ARTCL_STG_ID = art.ARTCL_STG_ID
													       and s1.trans_id='.$transID);
	
	return $groupedExternalKeys;
}











	public function createNewAttributeForCurrentTransID($label,$externalKey,$product)
	{
			$currentClass = $product->getConfigurationField(); 
			//TODO Hard Coded Config ID
			$attribute = CSPms::createField('',1614,CSITEM_POSITION_CHILD,'caption');
			$attribute->setLanguageDependance();
			$attribute->setValue('Label',$label);
			$attribute->setValue('ExternalKey',$externalKey);
			$attribute->store();
			
			$currentClass->addField($attribute->getID());
			$currentClass->store();
			
			return $attribute->getId();
	}
	
	
	public function storeNewAttributeIdstoTransactionMap($id,$externalKey)
	{
		$this->dataOperations->exec('Insert into cs_transaction_map values('."'".$id."'".","."'".$externalKey."')");
	}

	
	
	public function getAttributeLabelwithExternalLanguage($language,$externalKey)
	{

		$attributeLabel = $this->dataOperations->getAll('SELECT  Distinct a.Label FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
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



}
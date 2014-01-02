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
	 * Gets all the Updated Product Labels From the Staging Area
	 * @return $updatedProducts returns Array of Updated Product Label
	 */
	public function getAllUpdatedProductsLabels()
	{
		$updatedProducts = $this->dataOperations->getAll('select Label,EXTERNALKEY,Language_Code from cs_stg_incr_article where IS_DELETED =' ."'U'"." ".
										'and 
										TRANS_ID=(select MAX(TRANS_ID) from cs_stg_incr_article)');
		return $updatedProducts;
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
		
		foreach($updateID as $value)
		{
			$id = $value['CS_ATTR_KEY'];
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
	
	
	/**
	 * Get updated attribute values with respect to Product
	 * @return $updatedValues array of updated values with product attribute and language and respective externalkeys
	 */
	public function getUpdatedAttributeValuesWithProduct()
	{
		$updatedValues = $this->dataOperations->getAll('SELECT v.VALUE,v.Language_Code, a.ExternalKey as attrExt,art.EXTERNALKEY as pdExt FROM cs_stg_bridge_artcl_attribute s, cs_stg_incr_attribute a,
		 												cs_stg_incr_attribute_value v,cs_stg_incr_article art, cs_stg_bridge_attributes sb
		    											WHERE sb.ATTR_STG_ID = a.ATTR_STG_ID AND sb.ATTR_VALUE_STG_ID = v.ATTR_VALUE_STG_ID  
		    											and s.ATTR_STG_BRIDGE_ID = sb.ATTR_STG_BRIDGE_ID  and a.Language_Code= v.Language_Code
		    											and s.ARTCL_STG_ID = art.ARTCL_STG_ID 
		    											and s.IS_DELETED ='."'U'"." ".
        												'and s.TRANS_ID = (select MAX(TRANS_ID) from cs_stg_bridge_artcl_attribute)');
		return $updatedValues;
		
	}
	
	
	public function updateProductLabels($updatedLabels)
	{
		foreach ($updatedLabels as $label)
		{
			$product = CSPms::getProductForExternalKey($label['EXTERNALKEY']);
			$product->setValue('Label', $label['Label'],$this->importLanguage->checkIfLanguageExists($label['Language_Code']));
			$productId = $product->store(); 
			$product->checkin(); 
		}
	}
			
	
	public function updateAttributeLabels($updatedArticleLabels)
	{
		foreach ($updatedArticleLabels as $attrLabel)
		{
			$CSid = self::getCSIDForAttribute($attrLabel['EXTERNALKEY']);
			$attribute = CSPms::getField($CSid);
			$attribute->setValue('Label',$attrLabel['Label']);
			$attribute->store();
		}
	}
	
	
	public function updateAttributeValues($updatedValues,$languageArrayWithID)
	{
		foreach ($updatedValues as $value)
		{
			$product = CSPms::getProductForExternalKey($value['pdExt']);
			$product->checkout();
			$product->setValue(self::getCSIDForAttribute($value['attrExt']), $value['VALUE'],$languageArrayWithID[$value['Language_Code']]);
			$product->checkin();
			$product->store();
		}
	}
		
	
	
	
}
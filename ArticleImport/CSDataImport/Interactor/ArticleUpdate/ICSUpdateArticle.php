<?php

/**
 * Interface to Implement Updation of Articles
 * @author Anindya Gangakhedkar
 *
 */
interface ICSUpdateArticle
{
	public function getCSIDForAttribute($externalkey);
	public function updateAttributeLabels($updatedArticleLabels);
	public function getMaxTransCountForArticle();
	public function getCurrentPHPProcessCountforProduct();
	public function getPHPProcessCountForUpperBridge();
	public function getMaxTransIdForUpperBridge();
	public function createProduct($productfolderID,$language,$externalKey,$start,$end);
	public function getUniqueLanguageLabel($ExternalKey,$language,$start,$end);
	public function updateAttributeValues($start);
	public function createNewAttributeForCurrentTransID($label,$externalKey,$product);
	public function storeNewAttributeIdstoTransactionMap($id,$externalKey);
	public function UpdateAllLanguagesInContentServ();
	public function updateAllLabelsForExistingProduct($iExternalKey,$oProduct,$iStart,$iEnd,$aLanguageArray);
	public function getAllUpdatesForExistingProduct($iExternalKey,$start,$end);
	public function updateProductsGrouped($iStart,$iEnd);
	public function getGroupedProductsForUpdation($start,$end);
	
	
	
}
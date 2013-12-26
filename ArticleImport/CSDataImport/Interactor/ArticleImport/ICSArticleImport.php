<?php

/**
 * Interface For Article Import Operations.
 * @author Anindya Gangakhedkar
 *
 */
interface ICSArticleImport
{
	public function createProductFolder($sName);
	public function createLangDepedentProduct($productfolderID,$productNameArray);
	public function createProduct($sProductName,$productfolderID,$language,$externalKey);
	public function createConfiguration($configName);
	public function createAttribute($attributeName);
	public function getAllArticles();
	public function getProductAttributes($productID,$language);
	public function createClass($classname);
	public function getAllLanguagesFromStagingArea();
	public function getUniqueLanguageLabel($ExternalKey,$language);
	public function getAllDistinctExternalKey();
	public function getAttributeLabelwithExternalLanguage($language,$externalKey);
	public function getAllValueRange();
	public function getAllValuesforValueRange($externalkey,$valueRangeTypeID);
	public function getDistinctValueRangesValues($attributeid);
	public function getSingleValuesAttributes();
	public function getValueListFromAttributeResult($externalKey);
	public function getMultiValuedValuesforProduct($productid,$attributeId);
	public function concatValuesWithNewLine($array);
	public function getSingleValueAttributesforProduct($externalKey,$language);
	public function storeSingleValuedAttributes($currentConfigurationID,$currentClass,$singleValues);
	public function storeValueRanges($currentConfigurationID,$currentClass,$valueRanges,$attributeID);
	public function createProductAndAssignValues($attributeID,$currentClassID,$articles,$languageArrayWithID,$meyleDataFolderID);
	
	
	
}
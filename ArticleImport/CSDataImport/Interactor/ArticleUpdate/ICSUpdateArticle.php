<?php

/**
 * Interface to Implement Updation of Articles
 * @author Anindya Gangakhedkar
 *
 */
interface ICSUpdateArticle
{
	public function getAllUpdatedProductsLabels();
	public function getCSIDForAttribute($externalkey);
	public function getAllUpdateAttributeLabels();
	public function getUpdatedAttributeValuesWithProduct();
	public function updateProductLabels($updatedLabels);
	public function updateAttributeLabels($updatedArticleLabels);
	public function updateAttributeValues($updatedValues,$languageArrayWithID);
	
}
<?php
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleUpdate/CSUpdateArticle.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ImportLanguage/CSImportLanguage.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleImport/CSArticleImport.php';


$updateArticle = new CSUpdateArticle();
$articleImport = new CSArticleImport();
$importLanguage = new CSImportLanguage();

//Intializing the Timer
$iTimer = microtime(true);

//Get updated Article Labels with Language
$updatedLabels = $updateArticle->getAllUpdatedProductsLabels();
if(!empty($updatedLabels))
{
	$updateArticle->updateProductLabels($updatedLabels);
}

//Check for Updated Attribute Labels
$updatedArticleLabels = $updateArticle->getAllUpdateAttributeLabels();
//Check the Array is not Empty
if(!empty($updatedArticleLabels))
{
	$updateArticle->updateAttributeLabels($updatedArticleLabels);
}

//Check for Updated Values
$updatedValues = $updateArticle->getUpdatedAttributeValuesWithProduct();
//Get language array
$languageArrayWithID = $importLanguage->MakeLanguageArrayWithLanguageID($articleImport->getAllLanguagesFromStagingArea());
//check updated values array is not empty.
if (!empty($updatedValues))
{
	$updateArticle->updateAttributeValues($updatedValues,$languageArrayWithID);
}

//Printing to Total time Required
print_r(microtime(true)-$iTimer.'  '.'sec');
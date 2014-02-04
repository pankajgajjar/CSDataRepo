<?php
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleUpdate/CSUpdateArticle.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ImportLanguage/CSImportLanguage.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleImport/CSArticleImport.php';


$updateArticle = new CSUpdateArticle();
$articleImport = new CSArticleImport();
$importLanguage = new CSImportLanguage();

//Intializing the Timer
$iTimer = microtime(true);


$startProduct = $updateArticle->getCurrentPHPProcessCountforProduct();
$endProduct = $updateArticle->getMaxTransCountForArticle();
$startAttributeValues = $updateArticle->getPHPProcessCountForUpperBridge();

//Update Languages In ContentServ
$updateArticle->UpdateAllLanguagesInContentServ();


//Update All Products In COntentServ
 $updateArticle->updateProductsGrouped($startProduct,$endProduct);

 
 
//Update Attribute Labels in ContentServ
$updateArticle->updateProductAttributesGrouped($start);
 
 
 
 

//Printing to Total time Required
print_r(microtime(true)-$iTimer.'  '.'sec');
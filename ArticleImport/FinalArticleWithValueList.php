<?php
require_once 'CSArticleImport.php';
require_once 'CSImportLanguage.php';

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
$attributeMap = CSArticleImport::storeSingleValuedAttributes($currentConfigurationID,$currentClass,$singleValues);

//Getting All Distinct Value Ranges For Article Entity
$valueRanges = CSArticleImport::getAllValueRange();

//Traversing Value Ranges And Creating Them With Set Values In ENGLISH
$attributeID = CSArticleImport::storeValueRanges($currentConfigurationID,$currentClass,$valueRanges,$attributeMap);

//Getting all the Articles From the Staging Area
$articles = CSArticleImport::getAllArticles();

//Traversing All The Articles And Assigining Attributes to Them.
CSArticleImport::createProductAndAssignValues($attributeID,$currentClassID,$articles,$languageArrayWithID,$meyleDataFolderID);

//Storing The Transaction Map
CSStoreDataUsingRedBeanPHP::storeMappingTransactionTable($attributeID);

//Priting The Total Time Required
print_r(microtime(true)-$iTimer.'  '.'sec');


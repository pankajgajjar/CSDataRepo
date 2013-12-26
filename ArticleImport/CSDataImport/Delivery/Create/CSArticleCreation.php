<?php

require_once 'Interactor/ArticleImport/CSArticleImport.php';
require_once 'Interactor/StoreData/CSStoreDataUsingRedBeanPHP';
require_once 'Interactor/ImportLanguage/CSImportLanguage.php';

$dataImport = new CSArticleImport();
$storeData = new CSStoreDataUsingRedBeanPHP();
$importLanguage = new CSImportLanguage();


//Staring Timer To Check Performance
$iTimer = microtime(true);

//Creating The Product Folder
$meyleDataFolderID = $dataImport->createProductFolder('Meyle');

//Creating Class and Configuration For the Product And The Attributes
$currentClass = $dataImport->createClass('ArticleClass');
$currentClassID = $currentClass->store();
$currentConfigurationID = $dataImport->createConfiguration('ArticleConfiguration');

//Getting All languages From Staging Area and Inserting the Missing Languages
$languageArrayWithID = $importLanguage->MakeLanguageArrayWithLanguageID($dataImport->getAllLanguagesFromStagingArea());

//Getting All Single Values Attributes
$singleValues = $dataImport->getSingleValuesAttributes();

//Traversing the Single Valued Attributes And Storing them In ContentServ
$attributeMap = $dataImport->storeSingleValuedAttributes($currentConfigurationID,$currentClass,$singleValues);

//Getting All Distinct Value Ranges For Article Entity
$valueRanges = $dataImport->getAllValueRange();

//Traversing Value Ranges And Creating Them With Set Values In ENGLISH
$attributeID = $dataImport->storeValueRanges($currentConfigurationID,$currentClass,$valueRanges,$attributeMap);

//Getting all the Articles From the Staging Area
$articles = $dataImport->getAllArticles();

//Traversing All The Articles And Assigining Attributes to Them.
$dataImport->createProductAndAssignValues($attributeID,$currentClassID,$articles,$languageArrayWithID,$meyleDataFolderID);

//Storing The Transaction Map
$storeData->storeMappingTransactionTable($attributeID);

//Priting The Total Time Required
print_r(microtime(true)-$iTimer.'  '.'sec');


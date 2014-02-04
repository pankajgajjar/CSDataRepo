<?php

require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleImport/CSArticleImport.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/StoreData/CSStoreDataUsingRedBeanPHP.php';
require_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ImportLanguage/CSImportLanguage.php';


$dataImport = new CSArticleImport();
$storeData = new CSStoreDataUsingRedBeanPHP();
$importLanguage = new CSImportLanguage();


//Staring Timer To Check Performance
$iTimer = microtime(true);
//Creating The Product Folder
$meyleDataFolderID = $dataImport->createProductFolder('Meyle');

//Creating Class and Configuration For the Product And The Attributes
//CHANGE HERE LOOP FOR CLASSES
//Currently Dont have data for Classes
$currentClass = $dataImport->createClass('ArticleClass');
$currentClassID = $currentClass->store();
$currentConfigurationID = $dataImport->createConfiguration('ArticleConfiguration');

//Getting All languages From Staging Area and Inserting the Missing Languages
$languageArrayWithID = $importLanguage->MakeLanguageArrayWithLanguageID($dataImport->getAllLanguagesFromStagingArea());

//Getting All Single Values Attributes
//CHANGE HERE
$singleValues = $dataImport->getSingleValuesAttributes();

//Traversing the Single Valued Attributes And Storing them In ContentServ
//CHANGE HERE
$attributeMap = $dataImport->storeSingleValuedAttributes($currentConfigurationID,$currentClass,$singleValues);

//Getting All Distinct Value Ranges For Article Entity
//CHANGE HERE
$valueRanges = $dataImport->getAllValueRange();

//Traversing Value Ranges And Creating Them With Set Values In ENGLISH
//CHANGE HERE
$attributeID = $dataImport->storeValueRanges($currentConfigurationID,$currentClass,$valueRanges,$attributeMap);

//Getting all the Articles From the Staging Area
//CHANGE HERE
$articles = $dataImport->getAllArticles();

//Traversing All The Articles And Assigining Attributes to Them.
//CHANGE HERE
$dataImport->createProductAndAssignValues($attributeID,$currentClassID,$articles,$languageArrayWithID,$meyleDataFolderID);

//Storing The Transaction Map
//CHANGE HERE
$storeData->storeMappingTransactionTable($attributeID);

//Priting The Total Time Required
print_r(microtime(true)-$iTimer.'  '.'sec');


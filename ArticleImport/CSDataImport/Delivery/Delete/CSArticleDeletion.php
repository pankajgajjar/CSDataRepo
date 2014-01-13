<?php
include_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleDelete/CSDeleteArticle.php';

$iTimer = microtime(true);
$deleteArticle = new CSDeleteArticle();

$deleted = $deleteArticle->getDeletedArticles();

if(!empty($deleted))
{
	$deleteArticle->deleteProduct($deleted);
}

print_r(microtime(true)-$iTimer.'  '.'sec');
<?php
include_once '../'.CS::getProjectName().'/plugins/CSDataImport/Interactor/ArticleDelete/CSDeleteArticle.php';

$deleteArticle = new CSDeleteArticle();

$deleted = $deleteArticle->getDeletedArticles();

if(!empty($deleted))
{
	$deleteArticle->deleteProduct($deleted);
}
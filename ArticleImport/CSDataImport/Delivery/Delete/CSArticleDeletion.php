<?php
include_once 'Persistence/ArticleDelete/CSDeleteArticle';

$deleteArticle = new CSDeleteArticle();

$deleted = $deleteArticle->getDeletedArticles();

if(!empty($deleted))
{
	$deleteArticle->deleteProduct($deleted);
}
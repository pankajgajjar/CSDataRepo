<?php

/**
 * Interface to Implement Deletion of Articles
 * @author Anindya Gangakhedkar
 *
 */
interface ICSDeleteArticle
{
	public function getDeletedArticles();
	public function deleteProduct($deletedArticles);
}
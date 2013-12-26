<?php

include_once 'Persistence/CSDataOperations.php';
/**
 * Class to Delete Article From ContentServ
 *@author Anindya Gangakhedkar
 */
class CSDeleteArticle implements ICSDeleteArticle
{
	
	private $dataOperations;
	
	public function __construct()
	{
		$this->dataOperations = new CSDataOperations();
	}
	
	/**
	 * Gets all the Deleted Articles From the Staging Area
	 * @return $deletedArticles bean object of rows marked as delted in the staging area
	 */
	public function getDeletedArticles()
	{
		$deletedArticles = $dataOperations->getAll('select Label,ExternalKey 
									 from cs_stg_incr_article
									 where IS_DELETED='."'Y'"." ". 
									 'and TRANS_ID= (Select MAX(TRANS_ID) from cs_stg_incr_article) Group By ExternalKey');
		return $deletedArticles;
	}
	
	
	/**
	 * Deletes the Products from ContentServ
	 * @param $deletedArticles array of bean object for articles to be deleted
	 */
	public function deleteProduct($deletedArticles)
	{
		foreach ($deletedArticles as $delete)
		{
			$product = CSPms::getProductForExternalKey($delete['EXTERNALKEY']);
			$product->delete();
		}
		
	}
	


}
<?php



ini_set('max_execution_time', 3600);
ini_set('memory_limit', '2084M');
 

$value = $_GET['value'];

switch ($value)
{

	case 'update' :include_once '../'.CS::getProjectName().'/plugins/CSDataImport/Delivery/Update/CSArticleUpdation.php';
					break;
									
	case 'create':include_once '../'.CS::getProjectName().'/plugins/CSDataImport/Delivery/Create/CSArticleCreation.php';
					break;
					
	case 'delete' : include_once '../'.CS::getProjectName().'/plugins/CSDataImport/Delivery/Delete/CSArticleDeletion.php';
					break;
}

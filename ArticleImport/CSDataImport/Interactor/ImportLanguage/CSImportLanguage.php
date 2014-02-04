<?php
require_once 'ICSImportLanguage.php';

/**
 * Gets All Languages from Staging Area and Checks if they Exisist within ContentServ. If Not
 * Language is Created
 * @author Anindya Gangakhedkar
 *
 */
class CSImportLanguage implements ICSImportLanguage
{
	
	/**
	 * Checks Available Languages in ContentServ. 
	 * @return $languageArray with shortname as key and languageId as value
	 */
	public function CheckAvailableLanguages()
	{
		$languageArray= array();
		$language = CSLanguage::getLanguages();
		foreach($language as $singleLanguage)
		{
			$languageArray[$singleLanguage->getShortName()] = $singleLanguage->getID();
		}
		return $languageArray;
	}
	
	/**
	 *Checks if the language Exists Otherwise Inserts it
	 * @param unknown_type $language
	 * @return $newLanguageId returns languageID
	 */
	public function checkIfLanguageExists($language)
	{
		$languageArray = self::CheckAvailableLanguages();
		alert($language);
		if($languageArray[$language] == null)
		{
			$newLanguageId = self::InsertNewLanguage($language);
		}
		else 
		{
			$newLanguageId = $languageArray[$language];
		}
		return $newLanguageId;
	}
	
	
	/**
	 * Inserts New Language
	 * @param  $languageName name of the language
	 * @return returns languageId
	 */
	public function InsertNewLanguage($languageName)
	{
		$languageId = CSLanguage::createLanguage($languageName,$languageName);
		return $languageId;
	}
	
	
	/**
	 * Makes a Language Array with Id
	 * @param  $languageArray
	 * @return $languageIdArray language array with key as language and id as value.
	 */
	public function MakeLanguageArrayWithLanguageID($languageArray)
	{
		$languageIdArray = array();
		foreach($languageArray as $value)
		{
			$languageIdArray[$value] = self::checkIfLanguageExists($value);
		}
		
		return $languageIdArray;
		
	}
	
	
	

}
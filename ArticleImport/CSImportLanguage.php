<?php


class CSImportLanguage
{
	
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
	
	public function checkIfLanguageExists($language)
	{
		$languageArray = self::CheckAvailableLanguages();
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
	
	
	public function InsertNewLanguage($languageName)
	{
		$languageId = CSLanguage::createLanguage($languageName,$languageName);
		return $languageId;
	}
	
	
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
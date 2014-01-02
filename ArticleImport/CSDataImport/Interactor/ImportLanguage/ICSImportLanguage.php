<?php

/**
 * Interface to Define the functions required to Import Languages
 * @author Anindya Gangakhedkar
 *
 */
interface ICSImportLanguage
{
	public function CheckAvailableLanguages();
	public function checkIfLanguageExists($language);
	public function InsertNewLanguage($languageName);
	public function MakeLanguageArrayWithLanguageID($languageArray);	
}
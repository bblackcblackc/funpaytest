<?php
/**
 * Created by PhpStorm.
 * User: bc
 * Date: 02.05.19
 * Time: 2:22
 */

class ParseYAD {

    const keywordPass           = "Пароль:";
    const keywordSum            = "Спишется";
    const keywordAcc            = "Перевод на счет";

    const SMSEncoding           = "UTF-8";

    private const regExp        = "^" . self::keywordPass . "\s+([0-9]+).+" .
                                    self::keywordSum . "\s+([0-9\.\,]+).+" .
                                    self::keywordAcc . "\s+([0-9]+)";

    public $parseOk             = false;
    public $parseError          = "";
    public $parsePass           = "";
    public $parseSum            = 0;
    public $parseAccount        = 0;


    /**
     * @param $answer -- string for parsing
     * @return bool -- parsing result (correct = true)
     */
    function parseAnswer($answer) {

        try {

            $encIErr = mb_internal_encoding(self::SMSEncoding);
            $encRErr = mb_regex_encoding(self::SMSEncoding);

            if (!$encRErr or !$encIErr) {
                throw new Exception("Cannot set encoding.");
            }

            $searchInit = mb_ereg_search_init($answer, self::regExp);
            if (!$searchInit) {
                throw new Exception("Cannot init mb_ereg.");
            }

            $searchResult = mb_ereg_search();
            if (!$searchResult) {
                throw new Exception("No results or format error.");
            }

            $searchMatches = mb_ereg_search_getregs();
            if (($searchMatches === FALSE) or (count($searchMatches) != 4)) {
                throw new Exception("No results or extra results.");
            }

            $this->parsePass = $searchMatches[1];
            $this->parseSum = $searchMatches[2];
            $this->parseAccount = $searchMatches[3];

        } catch (Exception $exception) {
            $this->parseError = $exception->getMessage();
            $this->parseOk = false;
            return false;
        }

        $this->parseOk = true;
        return true;
    }

}

//$str = "Пароль: 2219
//Спишется 335.22
//Перевод на счет 410011867703873";
//$str = "Кошелек Яндекс.Денег указан неверно.";
//
//$parserInstance = new ParseYAD();
//if ($parserInstance->parseAnswer($str)) {
//    print("PASS " . $parserInstance->parsePass . PHP_EOL);
//    print("SUM " . $parserInstance->parseSum . PHP_EOL);
//    print("ACC " . $parserInstance->parseAccount . PHP_EOL);
//} else {
//    print("Error: " . $parserInstance->parseError . PHP_EOL);
//}

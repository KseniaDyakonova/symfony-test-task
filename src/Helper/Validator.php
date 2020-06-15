<?php

namespace App\Helper;

class Validator
{
    static public $simpleString = '/^[а-яА-Яa-zA-Z]{2,}$/u';
    static public $simplePhone = '/^[0-9]{6,15}$/';
    static public $password = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';

    static public function test($pattern, $stringToTest)
    {
        return preg_match(self::$$pattern, $stringToTest);
    }
}
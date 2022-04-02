<?php

namespace App;

class Encoder
{

    /**
     * @param $word
     * @return array|false|string|string[]|null
     */
    public function getWordUtf8Encoded($word)
    {
        $word = mb_strtolower($this->mb_trim($word));

        $encoding = mb_detect_encoding($word, [
            "UTF-8",
            "UTF-32",
            "UTF-32BE",
            "UTF-32LE",
            "UTF-16",
            "UTF-16BE",
            "UTF-16LE"
        ], true);

        if ($encoding !== "UTF-8") {
            $word = mb_convert_encoding($word, "UTF-8", $encoding);
        }
        return $word;
    }

    function mb_trim($str) {
        return preg_replace("/^\s+|\s+$/u", "", $str);
    }

}
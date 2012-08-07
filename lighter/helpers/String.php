<?php
namespace lighter\helpers;


class String {


    public static function camelize($string, $className = false) {
        $matches = array();
        // TODO place the "-" symbol into config so we can configure wich character
        // use to seperate words
        if (preg_match_all('([^\s-]+)', $string, $matches)) {
            $parts = current($matches);
            if (!$className) {
                $result = array_shift($parts);
            } else {
                $result = '';
            }
            foreach ($parts as $part) {
                $result.= ucfirst($part);
            }
            return $result;
        } else {
            return $string;
        }
    }

}


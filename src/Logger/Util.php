<?php

namespace Hurah\Logger;

class Util
{
    public static function stripNamespace(string $sLongName):string
    {
        if(preg_match('/\\\\/', $sLongName))
        {
            return array_reverse(explode('\\', $sLongName))[0];
        }
        return $sLongName;
    }

}
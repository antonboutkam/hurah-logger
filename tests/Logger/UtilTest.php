<?php

namespace Logger;

use Hurah\Logger\Util;

class UtilTest extends \PHPUnit\Framework\TestCase
{
    public function testStripNamespace()
    {
        $aTests = [
            'MeubelMens\\Backend\\EventHandler\\Product\\Variation\\{closure}' => '{closure}',
            '\\MeubelMens\\Backend\\EventHandler\\Product\\Variation\\{closure}' => '{closure}',
            'MeubelMens\\Backend\\EventHandler\\Product\\Variation\\someFunction' => 'someFunction',
            'anotherFunction' => 'anotherFunction',
            'MeubelMens\\Backend\\EventHandler\\Product\\Variation\\' => '',
        ];
        foreach ($aTests as $sTest => $sExpected)
        {
            $sResult = Util::stripNamespace($sTest);
            $this->assertEquals($sResult, $sExpected);
        }
    }
}
<?php declare(strict_types = 1);

namespace Happysir\Lib\Test\Enum;

use Happysir\Lib\Concern\BaseEnum;
use Happysir\Lib\Test\TestCase;

class TEnum extends BaseEnum
{
    public const TEST1 = '1';
    
    public const TEST2 = '2';
    
    protected static $nameMapping = [
        self::TEST1 => 'dasdsada',
        self::TEST2 => 'dasdsada213',
    ];
}

/**
 * Class TestEnum
 */
class TestEnum extends TestCase
{
    public function testEnum()
    {
        $this->assertEquals(TEnum::TEST1, '1');
        $this->assertEquals(TEnum::TEST2, '2');
        $this->assertEquals(
            TEnum::getValues(),
            [
                '1',
                '2',
            ]
        );
        $this->assertEquals(
            TEnum::getNameMapping(),
            [
                '1' => 'dasdsada',
                '2' => 'dasdsada213',
            ]
        );
        $this->assertEquals(
            TEnum::getConstants(),
            [
                'TEST1' => '1',
                'TEST2' => '2'
            ]
        );
        
        $this->assertTrue(TEnum::isValidField('TEST1'));
        $this->assertTrue(TEnum::isValidValue('2'));
        
        $this->assertEquals(
            TEnum::toLabelItems(),
            [
                ['value' => '1', 'label' => 'dasdsada'],
                ['value' => '2', 'label' => 'dasdsada213'],
            ]
        );
    }
}

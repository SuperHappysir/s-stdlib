<?php declare(strict_types = 1);

namespace Happysir\Lib\Enum;

use Happysir\Lib\Concern\BaseEnum;

/**
 * Class BoolEnum
 */
class BoolEnum extends BaseEnum
{
    public const TRUE  = 1;
    
    public const FALSE = 0;
    
    protected static $nameMapping = [
        self::FALSE => '否',
        self::TRUE  => '是',
    ];
}

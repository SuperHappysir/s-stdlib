<?php declare(strict_types = 1);

namespace Happysir\Lib\Concern;

use Exception;
use ReflectionClass;

/**
 * 基本枚举
 * Class BaseEnum
 */
abstract class BaseEnum
{
    /**
     * @var null 常量缓存数组
     */
    private static $constCacheArray;
    
    /**
     * 常量与名称映射表
     *
     * @var array
     */
    protected static $nameMapping = [];
    
    /**
     * BasicEnum constructor.
     */
    final private function __construct() { }
    
    /**
     * 获取常量
     *
     * @return array|mixed
     */
    public static function getConstants()
    {
        if (self::$constCacheArray === null) {
            self::$constCacheArray = [];
        }
        
        // 使用反射读取常量
        $calledClass = static::class;
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            try {
                $reflect = new ReflectionClass($calledClass);
            } catch (Exception $e) {
                return [];
            }
            
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        
        return self::$constCacheArray[$calledClass];
    }
    
    /**
     * 常量名称是否合法
     *
     * @param string $name
     * @param bool   $strict 是否严格模式
     *
     * @return bool
     */
    public static function isValidField(string $name, $strict = false) : bool
    {
        $constants = self::getConstants();
        
        if ($strict) {
            return array_key_exists($name, $constants);
        }
        
        $keys = array_map('strtolower', array_keys($constants));
        
        return in_array(strtolower($name), $keys, true);
    }
    
    /**
     * 属性值是否合法
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValidValue($value) : bool
    {
        $values = array_values(self::getConstants());
        
        return in_array($value, $values, true);
    }
    
    /**
     * 枚举
     *
     * @return array
     */
    public static function getValues() : array
    {
        return array_values(self::getConstants());
    }
    
    /**
     * 获取枚举常量对应的中文名称
     *
     * @param int|string $constant
     *
     * @return string
     */
    public static function getName($constant) : string
    {
        return static::$nameMapping[$constant] ?? '';
    }
    
    /**
     * getTranslations
     *
     * @return array
     */
    public static function getNameMapping() : array
    {
        return static::$nameMapping;
    }
    
    /**
     * 获取标签项
     *
     * @param string $labelName
     * @param string $valueName
     *
     * @return array
     */
    public static function toLabelItems(
        $labelName = 'label',
        $valueName = 'value'
    ) : array {
        $result = [];
        foreach (static::$nameMapping as $index => $item) {
            $result[] = [
                $labelName => $item,
                $valueName => $index,
            ];
        }
        
        return $result;
    }
    
}


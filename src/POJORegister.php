<?php declare(strict_types=1);


namespace Happysir\Lib;

use Happysir\Lib\Exception\POJOException;

/**
 * Class POJORegister
 *
 * @since 2.0
 */
class POJORegister
{
    /**
     * Dto array
     *
     * @var array
     *
     * @example
     * [
     *     'entityClassName' => 'class name'
     * ]
     */
    private static $POJO = [];

    /**
     * Fileds
     *
     * @var array
     *
     * @example
     * [
     *     'entityClassName' =>[
     *         'mapping' => [
     *             'attrName' => [
     *                 'field' => 'fieldName',
     *                 'pro' => 'proName',
     *                 'hidden' => false,
     *                 'type' => 'int',
     *             ]
     *         ]
     *     ]
     * ]
     */
    private static $fields = [];

    /**
     * Register `Dto`
     *
     * @param string $className
     * @param string $table
     * @param string $pool
     */
    public static function registerPOJO(string $className): void
    {
        self::$POJO[$className] = $className;
    }

    /**
     * Register `Filed`
     *
     * @param string $className
     * @param string $attrName
     * @param string $field
     * @param string $pro
     * @param bool   $hidden
     * @param string $type
     *
     * @throws DtoException
     */
    public static function registerFiled(
        string $className,
        string $attrName,
        string $field,
        string $pro,
        bool $hidden,
        string $type
    ): void {
        if (!isset(self::$POJO[$className])) {
            throw new POJOException(sprintf('%s must be `@POJO` to use `@Filed`', $className));
        }

        if (isset(self::$fields[$className]['reverse'][$field])) {
            throw new POJOException(sprintf('The `%s` name of `@Filed` has exist in %s', $className, $field));
        }

        self::$fields[$className]['mapping'][$attrName] = [
            'field' => $field,
            'pro'    => $pro,
            'hidden' => $hidden,
            'type'   => $type,
        ];

        self::$fields[$className]['reverse'][$field] = [
            'attr'   => $attrName,
            'pro'    => $pro,
            'hidden' => $hidden,
            'type'   => $type,
        ];
    }

    /**
     * Get field mapping
     *
     * @param string $className
     *
     * @return array
     */
    public static function getMapping(string $className): array
    {
        return self::$fields[$className]['mapping'] ?? [];
    }

    /**
     * Get field mapping
     *
     * @param string $className
     * @param string $field         配置的 field name
     *
     * @return array
     */
    public static function getReverseMappingByFiled(string $className, string $field): array
    {
        return self::$fields[$className]['reverse'][$field] ?? [];
    }
    
    /**
     * @return array
     */
    public static function getPOJO ()  {
        return self::$fields;
    }
}

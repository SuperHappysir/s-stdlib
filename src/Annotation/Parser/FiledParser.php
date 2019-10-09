<?php declare(strict_types = 1);

namespace Happysir\Lib\Annotation\Parser;

use Happysir\Lib\Annotation\Mapping\Filed;
use Happysir\Lib\POJORegister;
use ReflectionException;
use ReflectionProperty;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Stdlib\Helper\Str;
use function explode;
use function preg_match;
use function trim;

/**
 * Class ColumnParser
 *
 * @AnnotationParser(Filed::class)
 * @since 2.0
 */
class FiledParser extends Parser
{
    /**
     * @param int   $type
     * @param Filed $annotationObject
     *
     * @return array
     * @throws DbException
     * @throws ReflectionException
     */
    public function parse(int $type, $annotationObject) : array
    {
        $type = $this->getPropertyType();
        
        $name   = $annotationObject->getName();
        $prop   = $annotationObject->getProp();
        $hidden = $annotationObject->isHidden();
        $name   = empty($name) ? $this->propertyName : $name;
        $prop   = empty($prop) ? $this->propertyName : $prop;
        
        POJORegister::registerFiled($this->className, $this->propertyName, $name, $prop, $hidden, $type);
        
        return [];
    }
    
    /**
     * Get property
     *
     * @return string
     * @throws ReflectionException
     */
    private function getPropertyType() : string
    {
        // Parse php document
        $reflectProperty = new ReflectionProperty($this->className, $this->propertyName);
        $document        = $reflectProperty->getDocComment();
        
        if (!preg_match('/@var\s+([^\s]+)/', $document, $matches)) {
            return '';
        }
        
        $typeStr = $matches[1] ?? '';
        $types   = explode('|', $typeStr);
        
        foreach ($types as $type) {
            if (!Str::contains($type, 'null')) {
                return trim($type);
            }
        }
        
        return 'string';
    }
}

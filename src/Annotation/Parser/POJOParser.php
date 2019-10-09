<?php declare(strict_types = 1);

namespace Happysir\Lib\Annotation\Parser;

use Happysir\Lib\Annotation\Mapping\POJO;
use Happysir\Lib\POJORegister;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class POJOParser
 *
 * @AnnotationParser(POJO::class)
 * @since 2.0
 */
class POJOParser extends Parser
{
    /**
     * @param int $type
     * @param Dto $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject) : array
    {
        POJORegister::registerPOJO($this->className);
        
        return [$this->className, $this->className, Bean::PROTOTYPE, ''];
    }
}

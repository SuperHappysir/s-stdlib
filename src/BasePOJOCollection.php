<?php declare(strict_types = 1);

namespace Happysir\Lib;

use Swoft\Stdlib\Collection;

/**
 * Class POJO
 */
abstract class BasePOJOCollection extends Collection
{
    /**
     * @var string
     */
    protected $POJOClass = '';
    
    /**
     * Dto Object List constructor
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        
        $this->classExistOrFail();
        
        if (!empty($items)) {
            $items = $this->getArrayableItems($items);
            
            foreach ($items as $key => $item) {
                $this->pushToItems($key, $item);
            }
        }
    }
    
    /**
     * Create a new pojo
     *
     * @param array $attributes
     *
     * @return static
     * @throws DtoException
     */
    public static function new(array $attributes = []) : self
    {
        return new static($attributes);
    }
    
    public function push($value) : Collection
    {
        $this->classExistOrFail();
        
        if (!$value instanceof BasePOJO) {
            throw new \RuntimeException('item must be instanceof \Happysir\Lib\BasePOJO.');
        }
        
        return parent::push($value);
    }
    
    /**
     * getPOJOClass
     *
     * @return string|\Happysir\Lib\BasePOJO
     */
    public function getPOJOClass() : string
    {
        if (!$this->POJOClass) {
            throw new \RuntimeException('POJO class must to define.');
        }
        
        return $this->POJOClass;
    }
    
    /**
     * @return bool
     */
    protected function classExistOrFail() : bool
    {
        /**@var \Happysir\Lib\BasePOJO $POJOClass * */
        $POJOClass = $this->getPOJOClass();
        
        if ('' === $POJOClass) {
            throw new \RuntimeException('POJO class must to define.');
        }
        
        if (!class_exists($POJOClass)) {
            throw new ClassNotFoundException("class {$POJOClass} not found.");
        }
        
        return true;
    }
    
    /**
     * @param string $key
     * @param mixed  $item
     * @throws \Happysir\Lib\Exception\POJOException
     */
    protected function pushToItems($key, $item) : void
    {
        /**@var \Happysir\Lib\BasePOJO $POJOClass * */
        $POJOClass = $this->getPOJOClass();
        
        if ($item instanceof BasePOJO) {
            $this->items[$key] = $item;
        } else {
            if (is_array($item)) {
                $this->items[$key] = call_user_func("$POJOClass::new", $item);
                // $this->items[$key] = $POJOClass::new($item);
            } else {
                throw new \Happysir\Lib\Exception\POJOException('item must be instanceof \Happysir\Lib\BasePOJO.');
            }
        }
    }
}

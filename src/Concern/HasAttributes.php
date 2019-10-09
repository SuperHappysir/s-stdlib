<?php declare(strict_types = 1);

namespace Happysir\Lib\Concern;

use BadMethodCallException;
use Happysir\Lib\Exception\POJOException;
use Happysir\Lib\POJORegister;
use Swoft\Stdlib\Helper\ObjectHelper;
use TypeError;
use function in_array;

/**
 * Trait HasAttributes
 *
 * @package Epet\Db\Concern
 */
trait HasAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $modelAttributes = [];
    
    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     * @throws DtoException
     */
    public function attributesToArray()
    {
        $attributes = [];
        foreach ($this->getArrayableAttributes() as $key => $value) {
            [$pro, $value] = $this->getArrayableItem($key);
            if ($pro !== false) {
                $attributes[$pro] = $value;
            }
        }
        
        return $attributes;
    }
    
    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return array_merge($this->modelAttributes, $this->getModelAttributes());
    }
    
    /**
     * Get an attribute array of all arrayable values.
     *
     * @param string $key
     *
     * @return array
     * @throws DtoException
     */
    protected function getArrayableItem(string $key)
    {
        [$pro, $hidden, $value] = $this->getHiddenAttribute($key);
        // hidden status
        $hiddenStatus = $hidden
                        || in_array($key, $this->getModelHidden())
                        || in_array($pro, $this->getModelHidden());
        // visible status
        $visibleStatus = in_array($key, $this->getModelVisible())
                         || in_array($pro, $this->getModelVisible());
        
        if ($hiddenStatus === true && $visibleStatus === false) {
            return [false, false];
        }
        
        return [$pro, $value];
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return array
     * @throws DtoException
     */
    public function getModelAttribute(string $key) : array
    {
        [$attrName, , , $pro] = $this->getMappingByField($key);
        $getter = sprintf('get%s', ucfirst($attrName));
        
        if (!method_exists($this, $getter)) {
            throw new BadMethodCallException(
                sprintf('%s method(%s) is not exist!', static::class, $getter)
            );
        }
        
        $value = $this->{$getter}();
        
        return [$pro, $value];
    }
    
    /**
     * Get an attribute value from the model.
     *
     * @param string $key
     *
     * @return mixed
     * @throws DtoException
     */
    public function getAttributeValue(string $key)
    {
        return $this->getModelAttribute($key)[1];
    }
    
    /**
     * Get an not hidden attribute from the model.
     *
     * @param string $key
     *
     * @return array
     * @throws DtoException
     */
    public function getHiddenAttribute(string $key) : array
    {
        [$attrName, , $hidden, $pro] = $this->getMappingByField($key);
        $getter = sprintf('get%s', ucfirst($attrName));
        
        if (!method_exists($this, $getter)) {
            throw new BadMethodCallException(
                sprintf('%s method(%s) is not exist!', static::class, $getter)
            );
        }
        
        $value = $this->{$getter}();
        
        return [$pro, $hidden, $value];
    }
    
    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param        $value
     *
     * @return HasAttributes
     * @throws DtoException
     */
    public function setModelAttribute(string $key, $value) : self
    {
        [$attrName, $attType] = $this->getMappingByField($key);
        $setter = sprintf('set%s', ucfirst($attrName));
        $getter = sprintf('get%s', ucfirst($attrName));
        
        if (strrchr($attType, 'POJO') == 'POJO') {
            if (!class_exists($attType)) {
                throw new ClassNotFoundException("calss {$attType} not found.");
            }
            
            // $value = ($attType)::new($value)();
            $value = call_user_func("$attType::new", $item);
        } else {
            if (strrchr($attType, 'POJOCollection') == 'POJOCollection') {
                if (!class_exists($attType)) {
                    throw new ClassNotFoundException("calss {$attType} not found.");
                }
                
                $value = new $attType($value);
            } else {
                $value = ObjectHelper::parseParamType($attType, $value);
            }
        }
        
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        }
        
        return $this;
    }
    
    /**
     * Encode the given value as JSON.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value);
    }
    
    /**
     * Decode the given JSON back into an array or object.
     *
     * @param string $value
     * @param bool   $asObject
     *
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, !$asObject);
    }
    
    /**
     * Decode the given float.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromFloat($value)
    {
        switch ((string)$value) {
            case 'Infinity':
                return INF;
            case '-Infinity':
                return -INF;
            case 'NaN':
                return NAN;
            default:
                return (float)$value;
        }
    }
    
    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getModelAttributes()
    {
        $attributes = [];
        
        $mapping = POJORegister::getMapping($this->getClassName());
        
        foreach ($mapping as $attributeName => $map) {
            $getter = sprintf('get%s', ucfirst($attributeName));
            if (!method_exists($this, $getter)) {
                $getter = sprintf('is%s', ucfirst($attributeName));
                if (!method_exists($this, $getter)) {
                    continue;
                }
            }
            
            $field = $attributeName;
            if (isset($map['field']) && !empty($map['field'])) {
                $field = $map['field'];
            }
            
            try {
                $value = $this->{$getter}();
                $attributes[$field] = $value;
            } catch (TypeError $e) {
                unset($e);
                continue;
            }
        }
        
        return $attributes;
    }
    
    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param array $attributes
     *
     * @return $this
     * @throws DtoException
     */
    public function setRawAttributes(array $attributes)
    {
        foreach ($this->getSafeAttributes($attributes) as $key => $value) {
            $this->setModelAttribute($key, $value);
            $this->modelAttributes[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * Get safe model attributes
     *
     * @param array $attributes
     *
     * @return array
     */
    public function getSafeAttributes(array $attributes) : array
    {
        $safeAttributes = [];
        foreach ($attributes as $key => $value) {
            $field = POJORegister::getReverseMappingByFiled($this->getClassName(), $key);
            // not found this key column annotation
            if (empty($field)) {
                continue;
            }
            $type                 = $field['type'];
            $value                = ObjectHelper::parseParamType($type, $value);
            $safeAttributes[$key] = $value;
        }
        
        return $safeAttributes;
    }
    
    /**
     * Get a subset of the model's attributes.
     *
     * @param array $attributes
     *
     * @return array
     * @throws DtoException
     */
    public function only(array $attributes)
    {
        $results = [];
        
        foreach ($attributes as $attribute) {
            $results[$attribute] = $this->getAttributeValue($attribute);
        }
        
        return $results;
    }
    
    /**
     * @param string $key
     *
     * @return array
     * @throws DtoException
     */
    private function getMappingByField(string $key) : array
    {
        $mapping = POJORegister::getReverseMappingByFiled($this->getClassName(), $key);
        
        if (empty($mapping)) {
            throw new POJOException(sprintf('Filed(%s) is not exist!', $key));
        }
        
        $attrName = $mapping['attr'];
        $type     = $mapping['type'];
        $hidden   = $mapping['hidden'];
        $pro      = $mapping['pro'];
        
        return [$attrName, $type, $hidden, $pro];
    }
}

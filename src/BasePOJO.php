<?php declare(strict_types = 1);

namespace Happysir\Lib;

use Happysir\Lib\Concern\HasAttributes;
use Happysir\Lib\Concern\HidesAttributes;
use Happysir\Lib\Exception\POJOException;
use Swoft\Aop\Proxy;
use Swoft\Stdlib\Fluent;
use Swoft\Stdlib\Helper\JsonHelper;
use Throwable;

/**
 * Class POJO
 */
abstract class BasePOJO extends Fluent
{
    use HasAttributes, HidesAttributes;
    
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
        try {
            /* @var static $self */
            $self = bean(Proxy::getClassName(static::class));
        } catch (Throwable $e) {
            throw new POJOException($e->getMessage());
        }
        
        $self->fill($attributes);
        
        return $self;
    }
    
    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @return Model
     * @throws DtoException
     */
    public function fill(array $attributes) : self
    {
        $this->setRawAttributes($attributes);
        
        return $this;
    }
    
    /**
     * Convert the model instance to an array.
     *
     * @return array
     * @throws DtoException
     */
    public function toArray() : array
    {
        $attributes = $this->attributesToArray();
        foreach ($attributes as $key => $value) {
            if ($value instanceof BasePOJO
                || $value instanceof BasePOJOCollection) {
                $attributes[$key] = $value->toArray();
            }
        }
        
        return $attributes;
    }
    
    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     * @throws DtoException
     */
    public function toJson(int $options = 0) : string
    {
        return JsonHelper::encode($this->jsonSerialize(), $options);
    }
    
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     * @throws DtoException
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     *
     * @return bool
     * @throws DtoException
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getModelAttribute($offset));
    }
    
    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     * @throws DtoException
     */
    public function offsetGet($offset)
    {
        return $this->getAttributeValue($offset);
    }
    
    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     * @throws DtoException
     */
    public function offsetSet($offset, $value)
    {
        $this->setModelAttribute($offset, $value);
    }
    
    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->modelAttributes[$offset]);
    }
    
    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param string $key
     *
     * @return bool
     * @throws DtoException
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }
    
    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
    
    /**
     * @return string
     */
    protected function getClassName() : string
    {
        return Proxy::getClassName(static::class);
    }
    
    /**
     * Convert the model to its string representation.
     *
     * @return string
     * @throws DtoException
     */
    public function __toString()
    {
        return $this->toJson();
    }
}

<?php declare(strict_types = 1);

namespace Happysir\Lib\Concern;

use ArrayAccess;
use Happysir\Lib\Contract\Arrayable;
use Happysir\Lib\Contract\Jsonable;
use JsonSerializable;
use function array_key_exists;
use function count;
use function json_encode;

/**
 * Class Fluent
 *
 * @since 2.0
 */
class Fluent implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Create a new fluent instance.
     *
     * @param array|object $attributes
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
            
            $this->attributes[$key] = $value;
        }
    }
    
    /**
     * @param array $attributes
     * @return \Happysir\Lib\Concern\Fluent
     */
    public static function new(array $attributes) : self
    {
        return new static($attributes);
    }
    
    /**
     * Get an attribute from the fluent instance.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        return $default;
    }
    
    /**
     * Get the attributes from the fluent instance.
     *
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    
    /**
     * @return array
     */
    public function toArray() : array
    {
        $attrValue = (array)$this;
        
        return array_merge($this->attributes, $attrValue);
    }
    
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }
    
    /**
     * Convert the fluent instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0) : string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
    
    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->attributes[$offset]);
    }
    
    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        $this->attributes[$offset] = $value;
    }
    
    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        unset($this->attributes[$offset]);
    }
    
    /**
     * Handle dynamic calls to the fluent instance to set attributes.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'get') === 0
            && count($parameters) === 0
            && method_exists($this, $method)
        ) {
            return $this->{$method}();
        }
        
        $this->attributes[$method] = count($parameters)
                                     > 0 ? $parameters[0] : true;
        
        return $this;
    }
    
    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * Dynamically set the value of an attribute.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }
    
    /**
     * Dynamically check if an attribute is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }
    
    /**
     * Dynamically unset an attribute.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
}

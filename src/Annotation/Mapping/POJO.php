<?php declare(strict_types=1);


namespace Happysir\Lib\Annotation\Mapping;

/**
 * Class Dto
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @since 2.0
 */
class POJO
{
    /**
     * Dto name
     *
     * @var string
     */
    private $name = '';


    /**
     * Dto constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        if (isset($values['name'])) {
            $this->name = $values['name'];
        }

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

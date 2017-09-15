<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Api\Serializer;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class FieldsExclusionStrategy implements ExclusionStrategyInterface
{
    private $fields = array();

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        if (empty($this->fields)) {
            return false;
        }

        $name = $property->serializedName ?: $property->name;

        return !in_array($name, $this->fields);
    }
}

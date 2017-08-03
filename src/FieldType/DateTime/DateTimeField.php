<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\DateTime;

use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\DateTime\DateTimeInterface\DateTimeFieldType;
use Tardigrades\FieldType\FieldType;

class DateTimeField extends FieldType implements DateTimeFieldType
{
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        $formBuilder->add((string) $this->getConfig()->getHandle(), DateTimeType::class);

        return $formBuilder;
    }
}

<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\DateTime;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\DateTime\DateTimeInterface\DateTimeFieldType;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class DateTimeField extends FieldType implements DateTimeFieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

        if (!$this->hasEntityEvent('prePersist')) {

            $formBuilder->add(
                (string) $this->getConfig()->getHandle(),
                DateTimeType::class,
                [
                    'required' => $this->isRequired($section),
                    'format' => 'DD-mm-yyy H:i:s',
                    'data' => new \DateTime() // @todo: hmmm
                ]
            );
        }

        return $formBuilder;
    }
}

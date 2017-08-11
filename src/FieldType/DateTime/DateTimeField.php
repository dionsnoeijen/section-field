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

        try {
            $entityEvents = $this->getConfig()->getEntityEvents();
        } catch (\Exception $exception) {
            $entityEvents = [];
        }

        if (!in_array('prePersist', $entityEvents)) {

            try {
                $requiredFields = $section->getConfig()->getRequired();
            } catch (\Exception $exception) {
                $requiredFields = [];
            }

            $formBuilder->add(
                (string) $this->getConfig()->getHandle(),
                DateTimeType::class,
                [
                    'required' => in_array(
                        (string) $this->getConfig()->getHandle(),
                        $requiredFields
                    ),
                    'format' => 'DD-mm-yyy H:i:s'
                ]
            );
        }

        return $formBuilder;
    }
}

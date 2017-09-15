<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\DateTime;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class DateTimeField extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

        if (!$this->hasEntityEvent('prePersist')) {

            $formBuilder->add(
                (string) $this->getConfig()->getHandle(),
                DateTimeType::class,
                [
                    'format' => 'DD-mm-yyy H:i:s',
                    'data' => new \DateTime()
                ]
            );
        }

        return $formBuilder;
    }
}

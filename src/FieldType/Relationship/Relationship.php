<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Relationship\RelationshipInterface\Relationship as RelationshipInterface;

class Relationship extends FieldType implements RelationshipInterface
{

    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
//        $formBuilder->add((string) $this->getConfig()->getHandle(), EntityType::class, [
//            'class' => 'Tardigrades\\Blog\\Entity\\Blog',
//            'choice_label' => 'displayName',
//        ]);

        return $formBuilder;
    }
}

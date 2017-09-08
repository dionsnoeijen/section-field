<many-to-one field="<?php echo $thatHandle; ?>" fetch="EXTRA_LAZY" target-entity="<?php echo $thatFullyQualifiedClassName; ?>">
    <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" />
</many-to-one>

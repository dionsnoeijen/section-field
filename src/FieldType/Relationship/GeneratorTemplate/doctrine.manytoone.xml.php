<doctrine-mapping>
    <entity name="<?php echo $thisFullyQualifiedClassName; ?>">
        <many-to-one field="<?php echo $thatHandle; ?>" target-entity="<?php echo $thatFullyQualifiedClassName; ?>">
            <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" />
        </many-to-one>
    </entity>
</doctrine-mapping>

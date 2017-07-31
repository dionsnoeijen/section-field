<?php if ($type === 'unidirectional') { ?>
<doctrine-mapping>
    <entity name="<?php echo $thisFullyQualfiedClassName; ?>">
        <many-to-one field="<?php echo $thatHandle; ?>" target-entity="<?php echo $thatFullyQualfiedClassName; ?>">
            <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" />
        </many-to-one>
    </entity>
</doctrine-mapping>
<?php } ?>

<?php if ($type === 'bidirectional') { ?>
<doctrine-mapping>
    <entity name="<?php echo $thisFullyQualfiedClassName; ?>">
        <one-to-one field="<?php echo $thatHandle; ?>" target-entity="<?php echo $thatFullyQualfiedClassName; ?>" mapped-by="<?php echo $thisHandle; ?>" />
    </entity>
    <entity name="<?php echo $thatFullyQualfiedClassName; ?>">
        <one-to-one field="<?php echo $thisHandle; ?>" target-entity="<?php echo $thisFullyQualfiedClassName; ?>" inversed-by="<?php echo $thatHandle; ?>">
            <join-column name="<?php echo $thisHandle; ?>_id" referenced-column-name="id" />
        </one-to-one>
    </entity>
</doctrine-mapping>
<?php } ?>


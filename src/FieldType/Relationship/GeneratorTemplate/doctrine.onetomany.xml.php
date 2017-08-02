<?php if ($type === 'bidirectional') { ?>
<one-to-many field="<?php echo $thatPluralHandle; ?>" target-entity="<?php echo $thatFullyQualifiedClassName; ?>" mapped-by="<?php echo $thisHandle; ?>" />
</entity>
<entity name="<?php echo $thatFullyQualifiedClassName; ?>">
<many-to-one field="<?php echo $thisHandle; ?>" target-entity="<?php echo $thisFullyQualfiedClassName; ?>" inversed-by="<?php echo $thatPluralHandle; ?>">
    <join-column name="<?php echo $thisPluralHandle; ?>_id" referenced-column-name="id" />
</many-to-one>
<?php } ?>

<?php if ($type === 'unidirectional') { ?>
<many-to-many field="<?php echo $thatPluralHandle; ?>" target-entity="<?php echo $thatFullyQualfiedClassName; ?>">
    <join-table name="<?php echo $thisPluralHandle; ?>_<?php echo $thatPluralHandle; ?>">
        <join-columns>
            <join-column name="<?php echo $thisHandle; ?>_id" referenced-column-name="id" />
        </join-columns>
        <inverse-join-columns>
            <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" unique="true" />
        </inverse-join-columns>
    </join-table>
</many-to-many>
<?php } ?>

<?php if ($type === 'self-referencing') { ?>
<one-to-many field="children" target-entity="<?php echo $thatFullyQualfiedClassName; ?>" mapped-by="parent" />
<many-to-one field="parent" target-entity="<?php echo $thatFullyQualfiedClassName; ?>" inversed-by="children" />
<?php } ?>

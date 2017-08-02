<?php if ($type === 'unidirectional') { ?>
<many-to-many field="<?php echo $thatPluralHandle; ?>" target-entity="<?php echo $thatFullyQualifiedClassName; ?>">
    <join-table name="<?php echo $thisPluralHandle; ?>_<?php echo $thatPluralHandle; ?>">
        <join-columns>
            <join-column name="<?php echo $thisHandle; ?>_id" referenced-column-name="id" />
        </join-columns>
        <inverse-join-columns>
            <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" />
        </inverse-join-columns>
    </join-table>
</many-to-many>
<?php } ?>

<?php if ($type === 'bidirectional') { ?>
<many-to-many field="<?php echo $thisPluralHandle; ?>" mapped-by="<?php echo $thatPluralHandle; ?>" target-entity="<?php echo $thisFullyQualifiedClassName; ?>"/>
<?php } ?>

<?php if ($type === 'unidirectional') { ?>
<many-to-many field="<?php echo $toPluralHandle; ?>" target-entity="<?php echo $toFullyQualifiedClassName; ?>">
    <cascade>
        <cascade-all/>
    </cascade>
    <join-table name="<?php echo $fromPluralHandle; ?>_<?php echo $toPluralHandle; ?>">
        <join-columns>
            <join-column name="<?php echo $fromHandle; ?>_id" referenced-column-name="id" />
        </join-columns>
        <inverse-join-columns>
            <join-column name="<?php echo $toHandle; ?>_id" referenced-column-name="id" />
        </inverse-join-columns>
    </join-table>
</many-to-many>
<?php } ?>

<?php if ($type === 'bidirectional') { ?>
<many-to-many field="<?php echo $toPluralHandle; ?>" mapped-by="<?php echo $fromPluralHandle; ?>" target-entity="<?php echo $toFullyQualifiedClassName; ?>"/>
<?php } ?>

<?php if ($type === 'unidirectional') { ?>
<many-to-one field="<?php echo $toHandle; ?>" target-entity="<?php echo $toFullyQualfiedClassName; ?>">
    <join-column name="<?php echo $toHandle; ?>_id" referenced-column-name="id" />
</many-to-one>
<?php } ?>

<?php if ($type === 'bidirectional') { ?>
<one-to-one field="<?php echo $toHandle; ?>" target-entity="<?php echo $toFullyQualfiedClassName; ?>" mapped-by="<?php echo $fromHandle; ?>" />
</entity>
<entity name="<?php echo $toFullyQualfiedClassName; ?>">
<one-to-one field="<?php echo $fromHandle; ?>" target-entity="<?php echo $fromFullyQualfiedClassName; ?>" inversed-by="<?php echo $toHandle; ?>">
    <join-column name="<?php echo $toHandle; ?>_id" referenced-column-name="id" />
</one-to-one>
<?php } ?>

<?php if ($type === 'unidirectional') { ?>
<doctrine-mapping>
    <entity name="<?php echo $thisFullyQualifiedClassName; ?>">
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
    </entity>
</doctrine-mapping>
<?php } ?>

<?php if ($type === 'bidirectional') { ?>
<doctrine-mapping>
    <entity name="<?php echo $thisFullyQualifiedClassName; ?>">
        <many-to-many field="<?php echo $thatPluralHandle; ?>" inversed-by="<?php echo $thisPluralHandle; ?>" target-entity="<?php echo $thatFullyQualifiedClassName; ?>">
            <join-table name="<?php echo $thisPluralHandle; ?>_<?php echo $thatPluralHandle; ?>">
                <join-columns>
                    <join-column name="<?php echo $thisHandle; ?>_id" referenced-column-name="id" />
                </join-columns>
                <inverse-join-columns>
                    <join-column name="<?php echo $thatHandle; ?>_id" referenced-column-name="id" />
                </inverse-join-columns>
            </join-table>
        </many-to-many>
    </entity>
    <entity name="<?php echo $thisFullyQualifiedClassName; ?>">
        <many-to-many field="<?php echo $thisPluralHandle; ?>" mapped-by="<?php echo $thatPluralHandle; ?>" target-entity="<?php echo $thisFullyQualifiedClassName; ?>"/>
    </entity>
</doctrine-mapping>
<?php } ?>

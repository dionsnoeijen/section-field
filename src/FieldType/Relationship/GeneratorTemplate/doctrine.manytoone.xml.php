<doctrine-mapping>
    <entity name="User">
        <many-to-one field="address" target-entity="Address">
            <join-column name="address_id" referenced-column-name="id" />
        </many-to-one>
    </entity>
</doctrine-mapping>

<!-- UNIDIRECTIONAL -->
<doctrine-mapping>
    <entity name="User">
        <many-to-one field="address" target-entity="Address">
            <join-column name="address_id" referenced-column-name="id" />
        </many-to-one>
    </entity>
</doctrine-mapping>

<!-- BIDIRECTIONAL -->
<doctrine-mapping>
    <entity name="Customer">
        <one-to-one field="cart" target-entity="Cart" mapped-by="customer" />
    </entity>
    <entity name="Cart">
        <one-to-one field="customer" target-entity="Customer" inversed-by="cart">
            <join-column name="customer_id" referenced-column-name="id" />
        </one-to-one>
    </entity>
</doctrine-mapping>

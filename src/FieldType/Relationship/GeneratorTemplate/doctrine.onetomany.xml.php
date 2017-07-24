<!-- BIDIRECTIONAL -->
<doctrine-mapping>
    <entity name="Product">
        <one-to-many field="features" target-entity="Feature" mapped-by="product" />
    </entity>
    <entity name="Feature">
        <many-to-one field="product" target-entity="Product" inversed-by="features">
            <join-column name="product_id" referenced-column-name="id" />
        </many-to-one>
    </entity>
</doctrine-mapping>

<!-- One-To-Many, Unidirectional with Join Table -->
<doctrine-mapping>
    <entity name="User">
        <many-to-many field="phonenumbers" target-entity="Phonenumber">
            <join-table name="users_phonenumbers">
                <join-columns>
                    <join-column name="user_id" referenced-column-name="id" />
                </join-columns>
                <inverse-join-columns>
                    <join-column name="phonenumber_id" referenced-column-name="id" unique="true" />
                </inverse-join-columns>
            </join-table>
        </many-to-many>
    </entity>
</doctrine-mapping>

<!-- SELF REFERENCING -->
<doctrine-mapping>
    <entity name="Category">
        <one-to-many field="children" target-entity="Category" mapped-by="parent" />
        <many-to-one field="parent" target-entity="Category" inversed-by="children" />
    </entity>
</doctrine-mapping>

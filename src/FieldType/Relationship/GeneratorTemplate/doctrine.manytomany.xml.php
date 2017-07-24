<!-- MANY TO MANY, UNIDIRECTIONAL -->
<doctrine-mapping>
    <entity name="User">
        <many-to-many field="groups" target-entity="Group">
            <join-table name="users_groups">
                <join-columns>
                    <join-column name="user_id" referenced-column-name="id" />
                </join-columns>
                <inverse-join-columns>
                    <join-column name="group_id" referenced-column-name="id" />
                </inverse-join-columns>
            </join-table>
        </many-to-many>
    </entity>
</doctrine-mapping>

<!-- BIDIRECTIONAL -->
<doctrine-mapping>
    <entity name="User">
        <many-to-many field="groups" inversed-by="users" target-entity="Group">
            <join-table name="users_groups">
                <join-columns>
                    <join-column name="user_id" referenced-column-name="id" />
                </join-columns>
                <inverse-join-columns>
                    <join-column name="group_id" referenced-column-name="id" />
                </inverse-join-columns>
            </join-table>
        </many-to-many>
    </entity>
    <entity name="Group">
        <many-to-many field="users" mapped-by="groups" target-entity="User"/>
    </entity>
</doctrine-mapping>

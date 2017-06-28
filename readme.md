# SectionField

Many web applications share the common trait that it collects input from users. This input is transformed to output in various ways. The section field system aims to simplify and speed up the process of creating basic application functionality so focus can be on the awesome stuff.

It helps with: 
- Building advanced beautiful forms
- Creating template output
- Automated api endpoints

While the aim for this tool is to be very high level, simplicity should never prevail over flexibility.

## @todo (don't forget)

- Move FieldTypes to separate dependency
- Deleting a field type should ony be possible if there are no installed fields with that type.
- Field handles should not be created based on their name. At least. Maybe on creation when no handle is given, but an update should contain an explicit handle config to prevent application breaking changes just on updating a field name.
- Move field database interactions from commands to generic services.
- All commands that list something should check if there are entries.
- Refactor commands so shared functions are in a BaseClass.
- Unit tests for commands.

## Config

On the configuration of sections and fields

#### Why also yml section and field config?

For the development of the application itself the use of yml configurations is recommended because of having your application structure versioned.

However, in some situations, you would want users to be able to create or configure sections and fields through the use of the ui. Therefore a database structure is needed, regardless of where section data is stored. (EventStore, MySql, Mongo, ElasticSearch)

#### Example section config

	section:
    name: Comments
    fields:
      - name
      - email
      - comment
      - blog
    slug: [name]
    required: [name, email, comment, blog]
    default: name
    
name: Defines the section name, will be converted to handle aswel.
fields: Assign fields to this section as array
slug: What field should the section base it's slug on?
required: Required fields are defined on section level, not field level. It promotes reusability of the fields.
default: In a lot of cases you would want't to have access to a default field so the application won't break on changing fields in sections. The `default` config is required. All field types should be able to provide a value for default.

#### Example field configs

  ###### field/title.yml
	field:
	  name: Title
	  type: TextInput
	  length: 255
    
This is a simple text input field.

  ###### field/email.yml
	field:
	  name: Email
	  type: TextInput
	  length: 255
	  validate: email
	  
Another one, but with a validation specified.
	  
  ###### field/blog.yml
	field:
	  name: Blog
	  type: Relationship
	  variant: hidden
	  kind: many-to-one
	  to: blog

A more complicated field like a relationship requires a bit more explanation.

variant: There are many way's one might associate relationships. In this case the field is meanth for the `comment` section. Therefore the relationship is created without an explicit user input field and the variant hidden can generate a hidden field. Other variants might be: list, option select, multi select (in case of a one-to-many relationship)

## Commands

#### Field type commands

`bin/console sf:instal-field-type <namespace> (Escape \ in namespace)`

`bin/console sf:update-field-type (follow dialog)`

`bin/console sf:delete-field-type (follow dialog)`

`bin/console sf:list-field-type`


#### Field commands

`bin/console sf:create-field <path to config yml>`

`bin/console sf:update-field <path to config yml> (follow dialog)`

`bin/console sf:delete-field (follow dialog)`

`bin/console sf:list-field`


### Section commands

`bin/console sf:create-section <path to config yml>`

`bin/console sf:update-section <path to config yml> (follow dialog)`

`bin/console sf:delete-section (follow dialog)`

`bin/console sf:list-section`

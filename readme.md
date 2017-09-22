[![Build Status](https://travis-ci.org/dionsnoeijen/section-field.svg?branch=master)](https://travis-ci.org/dionsnoeijen/section-field)

# SectionField

Many web applications share the common trait that it collects input from users. This input is transformed to output in various ways. The section field system aims to simplify and speed up the process of creating basic application functionality so focus can be on the awesome stuff.

It helps with: 
- Building advanced beautiful forms
- Creating template output
- Automated api endpoints

While the aim for this tool is to be very high level, simplicity should never prevail over flexibility.

# Concept

Hereby a high level overview of the conceptual idea, real documentation is underway. (it really is...)

## Config

On the configuration of sections and fields

#### Why also yml section and field config?

For the development of the application itself the use of yml configurations is recommended because of having your application structure versioned.

However, in some situations, you would want users to be able to create or configure sections and fields through the use of the ui. Therefore a database structure is needed.

#### Language config

	language:
	  - nl_NL
	  - en_EN
    
location: ./language/language.yml

Your application will need at least one language. Configure it by adding it to an array that contains i18n language definitions.

Run the create language command and point to that language.yml. That way the database will be populated with the available languages.

#### Application config

	application:
	  name: Blog
	  handle: blog
	  languages:
	    - nl_NL
	    - en_EN

location: ./application/language.yml

You have to define at least one application. Every application has it's own application.yml config. You need a name, handle and languages that are available for this application. This provides multi-site or multi-application configurations for your platform.

#### Section config

	section:
	  name: Comments
	  handle: comments
	  fields:
	    - name
	    - email
	    - comment
	    - blog
	  slug: [name]
	  required: [name, email, comment, blog]
	  default: name
	  application: [blog]
    
A section contains fields. By this configuration things are tied together if you will. 

name: Defines the section name, will be converted to handle aswel.

handle: Your sections will be accessible by the use of a handle. For example, an endpoint to get entries that is generated for this section might look like this: `https://example.com/v1/section/comments`. Or accessing section data in your twig template might look like:

	{% for entry in section('comments').limit(10).read() %}
	    <h3>{{ entry.name }}</h3>
	    <p>{{ entry.email }}</p>
	    <p>{{ entry.comment }}</p>
	{% endfor %}

The keys in the config stand for:

fields: Assign fields to this section as array.

slug: What field should the section base it's slug on?

required: Required fields are defined on section level, not field level. It promotes reusability of the fields.

default: In a lot of cases you would want't to have access to a default field so the application won't break on changing fields in sections. The `default` config is required. All field types should be able to provide a value for default.

application: For what application(s) is this section available.

#### Example field configs

  ###### field/title.yml
	field:
	  name:
	   - nl_NL: Titel
	   - en_EN: Title
	  type: TextInput
	  length: 255
    
This is a simple text input field.

  ###### field/email.yml
	field:
	  name:
	    - nl_NL: Email
	    - en_EN: Email
	  type: TextInput
	  length: 255
	  validate: email
  
Another one, but with a validation specified.
  
  ###### field/blog.yml
	field:
	  name:
	    - nl_NL: Blog
	    - en_EN: Blog
	  type: Relationship
	  variant: hidden
	  kind: many-to-one
	  to: blog

A more complicated field like a relationship requires a bit more explanation.

variant: There are many way's one might associate relationships. In this case the field is meanth for the `comment` section. Therefore the relationship is created without an explicit user input field and the variant hidden can generate a hidden field. Other variants might be: list, option select, multi select (in case of a one-to-many relationship)

## Commands

#### Application commands

`bin/console sf:create-application <path to config yml>`

`bin/console sf:update-application <path to config yml>`

`bin/console sf:delete-application (follow dialog)`

`bin/console sf:list-application`


#### Language commands

`bin/console sf:create-language <path to config yml>`

`bin/console sf:update-language <path to config yml>`

`bin/console sf:delete-language (follow dialog)`

`bin/conole sf:list-language`


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


#### Section commands

`bin/console sf:create-section <path to config yml>`

`bin/console sf:update-section <path to config yml> (follow dialog)`

`bin/console sf:delete-section (follow dialog)`

`bin/console sf:list-section`

`bin/console sf:generate-section (follow dialog)`

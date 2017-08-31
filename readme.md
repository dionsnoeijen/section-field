[![Build Status](https://travis-ci.org/dionsnoeijen/section-field.svg?branch=master)](https://travis-ci.org/dionsnoeijen/section-field)

# SectionField

Many web applications share the common trait that it collects input from users. This input is transformed to output in various ways. The section field system aims to simplify and speed up the process of creating basic application functionality so focus can be on the awesome stuff.

It helps with: 
- Building advanced beautiful forms
- Creating template output
- Automated api endpoints

While the aim for this tool is to be very high level, simplicity should never prevail over flexibility.

## @todo (don't forget)

- Connect nullable field to required field.
- I have introduced the concept of JIT relationships. Right now tose fields are dectected by the presence of _id. Change that to _jit.
- We have a vo for FullyQualifiedClassName and SectionFullyQualifiedClassName. Take away the "Section" one.
- For many-to-many fields it's probably better to have two fields. One on each side.
- Doctrines inflector is really powerfull, use that instead of my helper methods.
- Move FieldTypes to separate dependency
- Deleting a field type should ony be possible if there are no installed fields with that type.
- Field handles should not be created based on their name. At least. Maybe on creation when no handle is given, but an update should contain an explicit handle config to prevent application breaking changes just on updating a field name.
- All commands that list something should check if there are entries.
- Finish up all types of relationships
- Unit tests for value objects.
- The symfony bundle will need a loader that adds classes to compile: https://symfony.com/doc/current/bundles/extension.html
- Make sure the use statements are only added once.

TODO (v1.0) V1.0 Is een volledig werkende setup (zie tab 1) voor Doctrine (MySQL) compatibiliteit.

- Input validatie (voor forms) toevoegen.
Dit vergt aanpassingen in de section config opties en de form afhandeling.
- Alle symfony field types toevoegen.
Symfony heeft een hele boel field types, deze moeten aan SectionField toegevoegd worden. Dit pas na de input validatie goed geintegreerd is.
- Endpoints (rest api voor secties)
Aanpassing aan section configuratie. Optie toevoegen om voor een sectie endpoints in te schakelen en daarbij eventueel welke endpoints beschikbaar gesteld moeten worden. Maak ook een endpoint per section waarmee je de section structuur kan ophalen.
- Alle relationship vormen in orde maken.
Nog niet alle relationship types werken goed. Hier moet nog extra tijd in gestoken worden.
- Update all command toevoegen.
Wanneer je een aanpassingen doet in de yml configuratie van bijvoorbeeld een field type of een sectie moet je een aantal handelingen verrichten. Maak een command dat meteen alles in orde maakt.
- Twig reader extension / reader service uitbreiden.
De twig reader (om sectie data naar een template te halen) is nu nog tamelijk beperkt, deze moet worden uitgebreid om de meest voorkomende functionaliteit te bevatten).
- Entry versioning toevoegen?
Misschien is dit niet direct nodig, maar de optie om entries te versionen kan relevant zijn. (anders v.1.1)
- Multilanguage toevoegen / afmaken?
Er zitten voorbereidingen in om multilanguage te kunnen werken met  de sections. Het is even de vraag of dit direct relevant is maar de integratie moet nog afgemaakt worden. (anders v.1.1)
- Unit tests afmaken.
De unit tests zijn incompleet, er moet nog veel onder unit test komen.

TODO (v1.1) v1.1 Voegt een aantal functionaliteiten toe om de flexibiliteit te vergroten.

Events... events everywhere
- Je wil kunnen acteren op bepaalde acties die voorkomen in het systeem. Dit kan met events. Denk bijvoorbeeld aan, onBeforeWrite, onAfterWrite, onRead, onGenerate... enz.
- Hooks... hooks everywhere
Met hooks kan je inhaken op de flow van de aplicatie om het verloop aan te passen.
- Extra readers
Het systeem kan lezen uit verschillende databronnen maar we hebben alleen nog maar een Doctrine reader. Er zijn een aantal relevante extra readers te bedenken. Waarschijnlijk willen we die in een aparte package leveren.
- Extra writers
Dit gaat samen met de readers. We kunnen schrijven naar verschillende databronnen.
- Entry versioning
- Entry multilanguage
- Application settings
Je kan applications configureren. Maak dit af.


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

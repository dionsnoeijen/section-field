<?php if ($kind === 'one-to-many' || $kind === 'many-to-many') { ?>
$this-><?php echo $pluralPropertyName; ?> = new ArrayCollection();
<?php } ?>

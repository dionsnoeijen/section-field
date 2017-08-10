<?php if ($kind === 'one-to-many' || $kind === 'many-to-many') { ?>
/** @var ArrayCollection */
protected $<?php echo $pluralPropertyName; ?>;
<?php } ?>

<?php if ($kind === 'many-to-one') { ?>
/** @var <?php echo $entity; ?> */
protected $<?php echo $propertyName; ?>;
<?php } ?>

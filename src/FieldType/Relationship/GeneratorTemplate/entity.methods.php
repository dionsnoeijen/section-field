<?php if ($kind === 'one-to-many') { ?>
public function get<?php echo $pluralMethodName; ?>(): Collection
{
    return $this-><?php echo $pluralPropertyName; ?>;
}

public function add<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    if ($this-><?php echo $pluralPropertyName; ?>->contains($<?php echo $propertyName; ?>)) {
        return $this;
    }
    $this-><?php echo $pluralPropertyName; ?>->add($<?php echo $propertyName; ?>);
    $<?php echo $propertyName; ?>->set<?php echo $thatMethodName; ?>($this);

    return $this;
}

public function remove<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    if (!$this-><?php echo $pluralPropertyName; ?>->contains($<?php echo $propertyName; ?>)) {
        return $this;
    }
    $this-><?php echo $pluralPropertyName; ?>->removeElement($<?php echo $propertyName; ?>);
    $<?php echo $propertyName; ?>->remove<?php echo $thatMethodName; ?>($this);

    return $this;
}
<?php } ?>

<?php if ($kind === 'many-to-one') { ?>
public function get<?php echo $methodName; ?>(): <?php echo $entity . PHP_EOL; ?>
{
    return $this-><?php echo $propertyName; ?>;
}

public function set<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    $this-><?php echo $propertyName; ?> = $<?php echo $propertyName; ?>;
}

public function remove<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    $this-><?php echo $propertyName; ?> = null;
}
<?php } ?>

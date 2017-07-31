public function get<?php echo $pluralMethodName; ?>(): Collection
{
    return $this-><?php echo $pluralPropertyName; ?>;
}

public function add<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    if ($this-><?php echo $pluralPropertyName; ?>->contains($<?php echo $propertyName; ?>) {
        return $this;
    }
    $this-><?php echo $pluralPropertyName; ?>->add($<?php echo $propertyName; ?>);
    $<?php echo $propertyName; ?>->set<?php echo $thatMethodName; ?>($this);

    return $this;
}

public function remove<?php echo $methodName; ?>(<?php echo $entity; ?> $<?php echo $propertyName; ?>): {{ section }}
{
    if (!$this-><?php echo $pluralPropertyName; ?>->contains($<?php echo $propertyName; ?>) {
        return $this;
    }
    $this-><?php echo $pluralPropertyName; ?>->removeElement($<?php echo $propertyName; ?>);
    $<?php echo $propertyName; ?>->remove<?php echo $thatMethodName; ?>($this);

    return $this;
}

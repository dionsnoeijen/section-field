<?php

namespace Tardigrades\FieldType\Slug;

use Tardigrades\FieldType\ValueObject\PrePersistTemplate;

interface SlugFieldType
{
    public function renderPrePersist(): PrePersistTemplate;
}

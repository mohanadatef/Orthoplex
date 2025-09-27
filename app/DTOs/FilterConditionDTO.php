<?php

namespace App\DTOs;

class FilterConditionDTO
{
    public function __construct(
        public string $field,
        public string $operator,
        public mixed $value
    ) {}
}

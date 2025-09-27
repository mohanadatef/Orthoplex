<?php

namespace App\DTOs;

class FilterGroupDTO
{
    /** @param array<int, FilterConditionDTO[]> $orGroups */
    public function __construct(
        public array $orGroups
    ) {}
}

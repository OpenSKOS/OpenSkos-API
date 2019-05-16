<?php

declare(strict_types=1);

namespace App\Institution;

interface InstitutionRepository
{
    /**
     * @return Institution[]
     */
    public function all(): array;
}

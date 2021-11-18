<?php
declare(strict_types=1);

namespace App\Contract;

interface CategoryServiceInterface
{
    public function getCategoryList(array $conditions=[], array $options=[], array $columns = ['*']): array;
}
<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseAttributeValueServiceInterface
{
    public function getMerchandiseAttributeValueList(array $conditions=[], array $options=[], array $columns = ['*']): array;
}
<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseItemAttributeServiceInterface
{
    /**
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getMerchandiseItemAttributeList(array $conditions=[], array $options=[], array $columns = ['*']) : array;
}
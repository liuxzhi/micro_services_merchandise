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
    public function getMerchandiseItemAttributeList($conditions=[], $options=[], array $columns = ['*']) : array;
}
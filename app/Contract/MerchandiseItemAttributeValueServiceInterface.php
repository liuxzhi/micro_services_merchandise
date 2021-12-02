<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseItemAttributeValueServiceInterface extends  AbstractServiceInterface
{
    /**
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getMerchandiseItemAttributeValueList(array $conditions=[], array $options=[], array $columns = ['*']) : array;
}
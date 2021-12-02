<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseServiceInterface extends  AbstractServiceInterface
{
    /**
     * 获取商品列表
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getMerchandiseList(array $conditions=[], array $options=[], array $columns = ['*']): array;
}

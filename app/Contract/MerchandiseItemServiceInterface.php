<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseItemServiceInterface extends  AbstractServiceInterface
{
    /**
     * 获取商品属性列表
     * @param array $conditions
     * @param array $options
     * @param array $columns
     * @return array
     */
    public function getMerchandiseItemList(array $conditions=[], array $options=[], array $columns = ['*']): array;
}

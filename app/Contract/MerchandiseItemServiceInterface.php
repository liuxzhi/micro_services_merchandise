<?php
declare(strict_types=1);

namespace App\Contract;

interface MerchandiseItemServiceInterface
{
    /**
     * 获取商品属性列表
     * @param array $conditions
     * @param array $options
     * @param array $columns
     * @return array
     */
    public function getMerchandiseItemListByCondition(array $conditions=[], array $options=[], array $columns = ['*']): array;
}
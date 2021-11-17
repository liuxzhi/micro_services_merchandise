<?php
declare(strict_types=1);

namespace App\Contract;

interface AttributeServiceInterface
{

    /**
     * 按条件查询属性列表
     * @param array $conditions
     * @param array $options
     * @param array $columns
     * @return array
     */
    public function getAttributeListByCondition($conditions=[], $options=[], array $columns = ['*']): array;
}
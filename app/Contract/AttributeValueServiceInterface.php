<?php
declare(strict_types=1);

namespace App\Contract;

interface AttributeValueServiceInterface
{
    /**
     * 根据查询条件获取属性值
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getAttributeValueList(array $conditions = [], array $options = [], array $columns = ['*']): array;
}
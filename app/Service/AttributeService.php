<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\AttributeServiceInterface;
use App\Model\Attribute;
use App\Model\Model;

class AttributeService extends AbstractService implements AttributeServiceInterface
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
    public function getAttributeList(array $conditions = [], array $options = [], array $columns = ['*']): array
    {
        $model = $this->getModelObject();

        $data = $this->optionWhere($model, $conditions, $options)
                     ->select($columns)
                     ->get();

        $data || $data = collect([]);

        return $data->toArray();

    }

    /**
     * @return Model
     */
    public function getModelObject() :Model
    {
        return make(Attribute::class);
    }
}
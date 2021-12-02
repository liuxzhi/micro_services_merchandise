<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemAttributeValueServiceInterface;
use App\Model\MerchandiseItemAttributeValue;
use App\Model\Model;

class MerchandiseItemAttributeValueService extends AbstractService implements MerchandiseItemAttributeValueServiceInterface
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
    public function getMerchandiseItemAttributeValueList(array $conditions = [], array $options = [], array $columns = ['*']): array
    {
        $model = $this->getModelObject();
        $data  = $this->optionWhere($model, $conditions, $options)
                      ->get();
        $data || $data = collect([]);

        return $data->toArray();
    }


    /**
     * 获取数据库操作对象
     *
     * @return MerchandiseItemAttributeValue|mixed
     */
    public function getModelObject() :Model
    {
        return make(MerchandiseItemAttributeValue::class);
    }
}
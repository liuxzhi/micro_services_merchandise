<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeValueServiceInterface;
use App\Model\MerchandiseAttributeValue;
use App\Model\Model;

class MerchandiseAttributeValueService extends AbstractService implements MerchandiseAttributeValueServiceInterface
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
    public function getMerchandiseAttributeValueList(array $conditions=[], array $options=[], array $columns = ['*']): array
    {
        $model = $this->getModelObject();
        $data = $this->optionWhere($model, $conditions, $options)->select($columns)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }


    /**
     * 获取数据库操作对象
     *
     * @return MerchandiseAttributeValue|mixed
     */
    public function getModelObject() :Model
    {
        return make(MerchandiseAttributeValue::class);
    }
}
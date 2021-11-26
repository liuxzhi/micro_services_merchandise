<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemAttributeValueServiceInterface;
use App\Model\MerchandiseItemAttributeValue;

/**
 * @method array get()
 * @method array create()
 * @method array list()
 * @method array update()
 * @method array delete()
 */
class MerchandiseItemAttributeValueService extends AbstractService implements MerchandiseItemAttributeValueServiceInterface
{

    /**
     * 根据查询条件获取属性值
     *
     * @param $conditions
     * @param $options
     *
     * @return array
     */
    public function getMerchandiseItemAttributeValueList($conditions = [], $options = []): array
    {
        $model = $this->getModelObject();
        $data  = $this->optionWhere($model, $conditions, $options)
                      ->get();
        $data || $data = collect([]);

        return $data->toArray();
    }


    /**
     * 获取数据库操作对象
     * @return Category|mixed
     */
    public function getModelObject()
    {
        return make(MerchandiseItemAttributeValue::class);
    }
}
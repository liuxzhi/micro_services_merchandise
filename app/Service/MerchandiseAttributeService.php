<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeServiceInterface;
use App\Model\MerchandiseAttribute;

/**
 * @method array get()
 * @method array create()
 * @method array list()
 * @method array update()
 * @method array delete()
 */
class MerchandiseAttributeService extends AbstractService implements MerchandiseAttributeServiceInterface
{


    /**
     * 根据查询条件获取属性值
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     * @return array
     */
    public function getMerchandiseAttributeList(array $conditions=[], array $options=[], array $columns = ['*']): array
    {
        $model = $this->getModelObject();
        $data = $this->optionWhere($model, $conditions, $options)->select($columns)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }


    /**
     * 获取数据库操作对象
     *
     * @return MerchandiseAttribute|mixed
     */
    public function getModelObject()
    {
        return make(MerchandiseAttribute::class);
    }

}
<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseServiceInterface;
use App\Model\Merchandise;
use App\Model\Model;


class MerchandiseService  extends AbstractService implements MerchandiseServiceInterface
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
    public function getMerchandiseList(array $conditions=[], array $options=[], array $columns = ['*']): array
    {
        $model = $this->getModelObject();
        $data = $this->optionWhere($model, $conditions, $options)->select($columns)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }

    /**
     * @return Merchandise|\Hyperf\DbConnection\Model\Model
     */
    public function getModelObject() :Model
    {
        return make(Merchandise::class);
    }

}
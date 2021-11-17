<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\AttributeValueServiceInterface;

class AttributeValueService extends AbstractService implements AttributeValueServiceInterface
{
    /**
     * AttributeValueService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 根据查询条件获取属性值
     *
     * @param $conditions
     * @param $options
     * @return array
     */
    public function getAttributeValueListByCondition($conditions=[], $options=[]): array
    {
        $model = new $this->modelClass();
        $data = $this->optionWhere($model, $conditions, $options)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }
}
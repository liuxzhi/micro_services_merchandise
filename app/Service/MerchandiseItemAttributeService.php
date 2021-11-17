<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseItemAttributeServiceInterface;

/**
 * @method array get()
 * @method array create()
 * @method array list()
 * @method array update()
 * @method array delete()
 */
class MerchandiseItemAttributeService extends AbstractService implements MerchandiseItemAttributeServiceInterface
{
    /**
     * MerchandiseItemAttributeService constructor.
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
    public function getMerchandiseItemAttributeListByCondition($conditions=[], $options=[]): array
    {
        $model = new $this->modelClass();
        $data = $this->optionWhere($model, $conditions, $options)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }
}
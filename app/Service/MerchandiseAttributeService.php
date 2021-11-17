<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeServiceInterface;

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
     * MerchandiseAttributeService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }


    /**
     * 根据查询条件获取属性值
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     * @return array
     */
    public function getMerchandiseAttributeListByCondition(array $conditions=[], array $options=[], array $columns = ['*']): array
    {
        $model = new $this->modelClass();
        $data = $this->optionWhere($model, $conditions, $options)->select($columns)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }

}
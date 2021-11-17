<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\CategoryServiceInterface;

/**
 * @method array get()
 * @method array create()
 * @method array list()
 * @method array update()
 * @method array delete()
 */
class CategoryService extends AbstractService implements CategoryServiceInterface
{
    /**
     * CategoryService constructor.
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
    public function getCategoryListByCondition($conditions=[], $options=[]): array
    {
        $model = new $this->modelClass();
        $data = $this->optionWhere($model, $conditions, $options)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }
}
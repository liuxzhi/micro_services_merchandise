<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\CategoryServiceInterface;
use App\Model\Category;

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
     * 根据查询条件获取属性值
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getCategoryList(array $conditions=[], array $options=[], array $columns= ['*']): array
    {
        $model = $this->getModelObject();
        $data = $this->optionWhere($model, $conditions, $options)->select($columns)->get();
        $data || $data = collect([]);
        return $data->toArray();
    }

    /**
     * 获取数据库操作对象
     * @return Category|mixed
     */
    public function getModelObject()
    {
        return make(Category::class);
    }
}
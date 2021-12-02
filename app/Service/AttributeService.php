<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\AttributeServiceInterface;
use App\Model\Attribute;
use App\Model\Model;

/**
 * @method array  create(array $params)
 * @method array  get(array $params, array $columns = ['*'])
 * @method int    update(array $params)
 * @method bool   updateByCondition(array $params, array $condition)
 * @method array  deleteByCondition(array $condition): bool
 * @method array  getList(array $conditions = [], array $options = [], array $columns = ['*'])
 * @method mixed  delete($params)
 * @method array  handleParams(array $params)
 * @method array  handlePagedData(array $dataWithPage, int $pageSize = 10)
 * @method Model  optionWhere(Model $model, array $conditions, array $options = []): Model
 * @method array  getDefaultPagedData($pageSize)
 */
class AttributeService extends AbstractService implements AttributeServiceInterface
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
    public function getAttributeList(array $conditions = [], array $options = [], array $columns = ['*']): array
    {
        $model = $this->getModelObject();

        $data = $this->optionWhere($model, $conditions, $options)
                     ->select($columns)
                     ->get();

        $data || $data = collect([]);

        return $data->toArray();

    }

    /**
     * @return Model
     */
    public function getModelObject() :Model
    {
        return make(Attribute::class);
    }
}
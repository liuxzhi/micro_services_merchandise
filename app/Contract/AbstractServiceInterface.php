<?php
declare(strict_types=1);

namespace App\Contract;

use App\Model\Model;
use Exception;


interface AbstractServiceInterface
{
    /**
     * @param array $params
     *
     * @return Model|\Hyperf\Database\Model\Model
     */
    public function create(array $params) : Model;


    /**
     * @param array          $params
     * @param array|string[] $columns
     *
     * @return array
     */
    public function get(array $params, array $columns = ['*']): array;

    /**
     * @param array $params
     *
     * @return int
     */
    public function update(array $params): int;


    /**
     * @param array $params
     * @param array $condition
     *
     * @return mixed
     */
    public function updateByCondition(array $params, array $condition);

    /**
     * 按条件删除
     *
     * @param array $condition
     *
     * @return mixed
     * @throws Exception
     */
    public function deleteByCondition(array $condition);


    /**
     * @param $params
     *
     * @return false|int|mixed
     */
    public function delete($params);


    /**
     * 处理参数.
     *
     * @param array $params 接受参数
     *
     * @return array 响应数组
     *
     */
    public function handleParams(array $params): array;


    /**
     * 数据分页处理
     *
     * @param array $dataWithPage
     * @param int   $pageSize
     *
     * @return array
     */
    public function handlePagedData(array $dataWithPage, int $pageSize = 10): array;

    /**
     * @param Model $model
     * @param array $conditions
     * @param array $options
     *
     * @return mixed
     */
    public function optionWhere(Model $model, array $conditions, array $options = []);

    /**
     * 获取分页为空时的默认数据
     *
     * @param $pageSize
     *
     * @return array
     */
    public function getDefaultPagedData($pageSize): array;


    /**
     * 获取列表数据
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    public function getList(array $conditions = [], array $options = [], array $columns = ['*']): array;

    /**
     * 获取model对象
     * @return mixed
     */
    public function getModelObject(): Model;

}

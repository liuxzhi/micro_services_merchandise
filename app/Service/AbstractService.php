<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Model;
use Exception;

abstract class AbstractService
{
    /**
     * @param array $params
     *
     * @return Model|\Hyperf\Database\Model\Model
     */
    public function create(array $params)
    {
        $model = $this->getModelObject();

        return $model->create($params);
    }


    /**
     * @param array          $params
     * @param array|string[] $columns
     *
     * @return array
     */
    public function get(array $params, array $columns = ['*']): array
    {
        $model = $this->getModelObject();

        $info = $model->select($columns)
                      ->where($params)
                      ->get();
        if ($info) {
            return $info->toArray();
        }

        return [];
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function update(array $params): int
    {
        $model = $this->getModelObject();
        $id    = (int)$params['id'];
        unset($params['id']);

        return $model->where(['id' => $id])
                     ->update($params);
    }


    /**
     * @param array $params
     * @param array $condition
     *
     * @return bool
     */
    public function updateByCondition(array $params, array $condition): bool
    {
        $model = $this->getModelObject();

        return $this->optionWhere($model, $condition)
                    ->update($params);
    }

    /**
     * 按条件删除
     *
     * @param array $condition
     *
     * @return bool
     * @throws Exception
     */
    public function deleteByCondition(array $condition): bool
    {
        $model = $this->getModelObject();

        return $this->optionWhere($model, $condition)
                    ->delete();
    }


    /**
     * @param $params
     *
     * @return false|int|mixed
     */
    public function delete($params)
    {
        $model = $this->getModelObject();
        $id    = (int)$params['id'];

        return $model->where(['id' => $id])
                     ->delete();

    }


    /**
     * 处理参数.
     *
     * @param array $params 接受参数
     *
     * @return array 响应数组
     *
     */
    protected function handleParams(array $params): array
    {

        $options = [
            'pageSize'   => $params['pageSize'] ?? 10,
            'page'       => $params['page'] ?? 1,
            'orderByRaw' => $params['orderByRaw'] ?? 'id desc',
        ];

        unset($params['pageSize']);
        unset($params['page']);
        unset($params['orderByRaw']);
        $where = !empty($params) ? $params : [];

        return [$where, $options];
    }


    /**
     * 数据分页处理
     *
     * @param array $dataWithPage
     * @param int   $pageSize
     *
     * @return array
     */
    public function handlePagedData(array $dataWithPage, int $pageSize = 10): array
    {
        if (!$dataWithPage) {
            return $this->getDefaultPagedData($pageSize);
        }

        $data['page']['pageSize']  = $dataWithPage['per_page'];
        $data['page']['total']     = $dataWithPage['total'];
        $data['page']['totalPage'] = $dataWithPage['last_page'];
        $data['page']['page']      = $dataWithPage['current_page'];

        $itemsList = [];
        foreach ($dataWithPage['data'] as $key => $items) {
            $itemsList[$key] = $items;
        }

        $data['list'] = $itemsList;

        return $data;
    }

    /**
     * @param Model $model
     * @param array $conditions
     * @param array $options
     *
     * @return Model
     */
    public function optionWhere(Model $model, array $conditions, array $options = []): Model
    {
        if (!empty($conditions) && is_array($conditions)) {

            foreach ($conditions as $k => $v) {
                if (!is_array($v)) {
                    $model = $model->where($k, $v);
                    continue;
                }

                if (is_numeric($k)) {
                    $v[1]    = mb_strtoupper($v[1]);
                    $boolean = $v[3] ?? 'and';
                    if (in_array($v[1], ['=', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'])) {
                        $model = $model->where($v[0], $v[1], $v[2], $boolean);
                    } elseif ($v[1] == 'IN') {
                        $model = $model->whereIn($v[0], $v[2], $boolean);
                    } elseif ($v[1] == 'NOT IN') {
                        $model = $model->whereNotIn($v[0], $v[2], $boolean);
                    } elseif ($v[1] == 'RAW') {
                        $model = $model->whereRaw($v[0], $v[2], $boolean);
                    } elseif ($v[1] == "BETWEEN") {
                        $model = $model->whereBetween($v[0], $v[2], $boolean);
                    }
                } else {
                    $model = $model->whereIn($k, $v);
                }
            }

        }

        isset($options['orderByRaw']) && $model = $model->orderByRaw($options['orderByRaw']);

        return $model;
    }

    /**
     * 获取分页为空时的默认数据
     *
     * @param $pageSize
     *
     * @return array
     */
    protected function getDefaultPagedData($pageSize): array
    {
        return [
            'page' => [
                'pageSize'  => $pageSize,
                'total'     => '0',
                'totalPage' => '0',
                'page'      => 1,
            ],
            'list' => [],
        ];

    }


    /**
     * 获取列表数据
     *
     * @param array $conditions
     * @param array $options
     * @param array $columns
     *
     * @return array
     */
    protected function getList(array $conditions = [], array $options = [], array $columns = ['*']): array
    {
        $model              = $this->getModelObject();
        $modelWithCondition = $this->optionWhere($model, $conditions, $options);

        // 分页数据
        if (isset($options['page'])) {

            $data         = [];
            $pageSize     = isset($options['pageSize']) ? (int)$options['pageSize'] : 10;
            $pageName     = 'page';
            $page         = isset($options['page']) ? (int)$options['page'] : 1;
            $dataWithPage = $model->paginate($pageSize, $columns, $pageName, $page);

            if ($dataWithPage) {
                $data = $dataWithPage->toArray();
            }

            return $this->handlePagedData($data, $pageSize);
        }

        // 全量数据
        $data = $modelWithCondition->select($columns)
                                   ->get();

        $data || $data = collect([]);

        return $data->toArray();
    }


    /**
     * 获取model对象
     * @return mixed
     */
    abstract public function getModelObject(): Model;

}

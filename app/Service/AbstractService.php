<?php
declare(strict_types=1);

namespace App\Service;

use Hyperf\Database\Model\Model;

abstract class AbstractService
{

    /**
     * @var Model
     */
    protected $modelClass = "";

    public function __construct()
    {
        if (!$this->modelClass) {
            $modelClass = str_replace(['\Service', 'Service'], ['\Model', ''], get_class($this));
            if (!class_exists($modelClass)) {
                throw new \Exception("model " . $modelClass . "isn't exist");
            }
            $this->setModelClass($modelClass);
        }
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function create($params)
    {
        $model = new $this->modelClass();

        return $model->create($params);
    }

    /**
     * @param $params
     * @param $columns
     *
     * @return mixed
     */
    public function get($params, array $columns = ['*'])
    {
        $model = new $this->modelClass();
        if (!isset($params['is_delete'])) {
            $params['is_delete'] = 0;
        }

        $info = $model->select($columns)
            ->where($params)
            ->get();
        if ($info) {
            return $info->toArray();
        }

        return [];
    }

    /**
     *  列表查询
     *
     * @param $params
     * @param $columns
     *
     * @return array
     */
    public function list($params, $columns = ["*"])
    {
        $model = new $this->modelClass();
        if (!isset($params['is_delete'])) {
            $params['is_delete'] = 0;
        }

        [$where, $options] = $this->handleParams($params);
        $model = $this->optionWhere($model, $where, $options);

        // 分页参数
        $perPage = isset($options['perPage']) ? (int)$options['perPage'] : 10;
        $pageName = $options['pageName'] ?? 'page';
        $page = isset($options['page']) ? (int)$options['page'] : null;

        // 分页
        $data = $model->paginate($perPage, $columns, $pageName, $page);
        if ($data) {
            $dataWithPage = $data->toArray();

            return $this->handleData($dataWithPage);
        }

        $default = [
            'page' => [
                'perPage' => $perPage,
                'total' => '0',
                'totalPage' => '0',
                'currentPage' => 1,
            ],
            'list' => [],
        ];

        return $default;

    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function update($params)
    {
        $model = new $this->modelClass();
        $id = (int)$params['id'];
        unset($params['id']);

        return $model->where(['id' => $id])
            ->update($params);
    }

    /**
     * @param $params
     * @param $softDelete
     *
     * @return mixed
     */
    public function delete($params, $softDelete = true)
    {
        $model = new $this->modelClass();
        $id = (int)$params['id'];

        if ($softDelete) {
            return $model->where(['id' => $id])
                ->update(['is_delete' => 1]);
        }

        return $model->where(['id' => $id])
            ->delete();
    }


    /**
     * 获取操作的模型名称
     * @return \Hyperf\Database\Model\Model
     */
    protected function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * 设置操作的模型名称
     *
     * @param $className
     */
    protected function setModelClass($className)
    {
        $this->modelClass = $className;
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
            'perPage' => $params['perPage'] ?? 10,
            'page' => $params['page'] ?? 1,
            'orderByRaw' => $params['orderByRaw'] ?? 'id desc',
        ];

        unset($params['perPage']);
        unset($params['page']);
        unset($params['orderByRaw']);
        $where = !empty($params) ? $params : [];

        return [$where, $options];
    }

    /**
     * 数据分页处理
     *
     * @param array $dataWithPage
     *
     * @return array
     */
    protected function handleData(array $dataWithPage): array
    {
        $data['page']['perPage'] = $dataWithPage['per_page'];
        $data['page']['total'] = $dataWithPage['total'];
        $data['page']['totalPage'] = $dataWithPage['last_page'];
        $data['page']['currentPage'] = $dataWithPage['current_page'];

        $itemsList = [];
        foreach ($dataWithPage['data'] as $key => $items) {
            $itemsList[$key] = $items;
        }

        $data['list'] = $itemsList;

        return $data;
    }

    /**
     * @param       $model
     * @param array $where
     * @param array $options
     *
     * @return static
     */
    public function optionWhere($model, array $where, array $options = [])
    {
        if (!empty($where) && is_array($where)) {
            foreach ($where as $k => $v) {

                if (!is_array($v)) {
                    $model = $model->where($k, $v);
                    continue;
                }

                if (is_numeric($k)) {
                    $v[1] = mb_strtoupper($v[1]);
                    $boolean = isset($v[3]) ? $v[3] : 'and';
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
}

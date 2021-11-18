<?php
declare(strict_types=1);

namespace App\Service;


abstract class AbstractService
{
    /**
     * @var string
     */
    protected $modelClass = "";

    /**
     * AbstractService constructor.
     * @throws \Exception
     */
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

        $info = $model->select($columns)
                      ->where($params)
                      ->get();
        if ($info) {
            return $info->toArray();
        }

        return [];
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function update($params)
    {
        $model = new $this->modelClass();
        $id    = (int)$params['id'];
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
        $id    = (int)$params['id'];

        if ($softDelete) {
            return $model->where(['id' => $id])
                         ->update(['deleted_at' => time()]);
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
            'pageSize'    => $params['pageSize'] ?? 10,
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
    protected function handlePagedData(array $dataWithPage, int $pageSize): array
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
     * @param       $model
     * @param array $conditions
     * @param array $options
     *
     * @return static
     */
    public function optionWhere($model, array $conditions, array $options = [])
    {
        if (!empty($conditions) && is_array($conditions)) {
            foreach ($conditions as $k => $v) {
                if (!is_array($v)) {
                    $model = $model->where($k, $v);
                    continue;
                }

                if (is_numeric($k)) {
                    $v[1]    = mb_strtoupper($v[1]);
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

    /**
     * 获取分页为空时的默认数据
     *
     * @param $pageSize
     *
     * @return array
     */
    protected function getDefaultPagedData($pageSize)
    {
        $defaultPageData = [
            'page' => [
                'pageSize'  => $pageSize,
                'total'     => '0',
                'totalPage' => '0',
                'page'      => 1,
            ],
            'list' => [],
        ];

        return $defaultPageData;
    }

}

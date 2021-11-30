<?php

declare(strict_types=1);

namespace App\Logic\Merchandise;


use Hyperf\Di\Annotation\Inject;

use App\Contract\MerchandiseServiceInterface;
use App\Contract\MerchandiseAttributeServiceInterface;
use App\Contract\MerchandiseAttributeValueServiceInterface;
use App\Contract\MerchandiseItemServiceInterface;
use App\Contract\MerchandiseItemAttributeServiceInterface;
use App\Contract\MerchandiseItemAttributeValueServiceInterface;
use App\Contract\AttributeServiceInterface;
use App\Contract\AttributeValueServiceInterface;

use App\Exception\BusinessException;
use App\Constants\BusinessErrorCode;
use App\Helper\Log;
use throwable;
use Hyperf\DbConnection\Db;


class MerchandiseHandler
{
    /**
     * @Inject
     * @var AttributeServiceInterface
     */
    protected $AttributeService;

    /**
     * @Inject
     * @var AttributeValueServiceInterface
     */
    protected $AttributeValueService;

    /**
     * @Inject
     * @var MerchandiseServiceInterface
     */
    protected $MerchandiseService;

    /**
     * @Inject
     * @var MerchandiseAttributeServiceInterface
     */
    protected $MerchandiseAttributeService;

    /**
     * @Inject
     * @var MerchandiseAttributeValueServiceInterface
     */
    protected $MerchandiseAttributeValueService;

    /**
     * @Inject
     * @var MerchandiseItemServiceInterface
     */
    protected $MerchandiseItemService;

    /**
     * @Inject
     * @var MerchandiseItemAttributeServiceInterface
     */
    protected $MerchandiseItemAttributeService;


    /**
     * @Inject
     * @var MerchandiseItemAttributeValueServiceInterface
     */
    protected $MerchandiseItemAttributeValueService;

    /**
     * @param $params
     *
     * @return array
     * @throws throwable
     */
    public function create($params)
    {
        try {
            Db::beginTransaction();

            // 创建商品基本信息(SPU)
            $merchandiseId = $this->createMerchandise($params);
            // 创建商品属性和属性值(SPU)
            $this->createMerchandiseAttributeValue($merchandiseId, $params);
            // 创建商品对应单品(SKU)
            $this->createMerchandiseItems($merchandiseId, $params);

            Db::commit();
        } catch (throwable $throwable) {
            Db::rollBack();
            Log::error("create_merchandise_error", ['params' => $params, "message" => $throwable->getMessage()]);
            throw $throwable;
        }

        return ['merchandise_id' => $merchandiseId];
    }

    /**
     * 获取商品(spu)和sku
     * @param $params
     * @param $columns
     *
     * @return mixed
     */
    public function getMerchandiseAssociatedMerchandiseItemsList(array $params = [], array $columns = ['*'])
    {
        $merchandiseModel = $this->MerchandiseService->getModelObject();

        $pageSize = isset($options['pageSize']) ? (int)$params['pageSize'] : 10;
        $pageName = 'page';
        $page     = isset($options['page']) ? (int)$params['page'] : 1;

        $merchandiseInfo = $merchandiseModel->where(['merchandise.id' => $params['id']])
                                            ->join("merchandise_item", "merchandise.id", "=",
                                                "merchandise_item.merchandise_id", 'left')
                                            ->paginate($pageSize, $columns, $pageName, $page);

        return $this->MerchandiseService->handlePagedData($merchandiseInfo->toArray(), 10);
    }

    /**
     * 获取商品详情SPU 和 SKU列表
     *
     * @param $params
     *
     * @return mixed
     */
    public function get(array $params)
    {
        $merchandiseId = $params['merchandise_id'];

        $merchandiseInfo = $this->MerchandiseService->get(['id' => $merchandiseId], ['id', 'name', 'introduction'])[0];

        $itemList = $this->MerchandiseItemService->getMerchandiseItemList(['merchandise_id' => $merchandiseId],
            ['orderByRaw' => 'id asc'], [
                'id',
                'merchandise_id',
                'merchandise_no',
                'storage',
                'name',
                'image',
                'attribute_ids',
                'attribute_value_ids'
            ]);

        $merchandiseItemId          = $params['item_id'] ?? $itemList[0]['id'];
        $merchandiseInfo['item_id'] = $merchandiseItemId;

        $merchandiseAttributeValueAssociatedList = $this->getMerchandiseAttributeValueAssociatedList($merchandiseId);

        // 单品信息(item)
        $merchandiseItemList = $this->formatMerchandiseItemList($itemList, $merchandiseItemId);
        // 获取选中的属性值
        $itemCheckedAttributeValue = $this->getCheckedAttributeValue($merchandiseItemList);

        $this->doCheckedAttributeValue($merchandiseAttributeValueAssociatedList, $itemCheckedAttributeValue);

        $merchandiseInfo['attribute_value_associated_list'] = $merchandiseAttributeValueAssociatedList;
        $merchandiseInfo['item_list']                       = $merchandiseItemList;

        return $merchandiseInfo;
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws throwable
     */
    public function update(array $params)
    {

        $merchandiseAttributeList = $this->MerchandiseAttributeService->getMerchandiseAttributeList(['merchandise_id' => $params['id']],
            [], ['attribute_id', 'merchandise_id', 'id']);
        // 验证参数
        $this->validateExtendedUpdateParams($params, $merchandiseAttributeList);

        $merchandiseId = $params['id'];
        try {
            Db::beginTransaction();

            // 更新商品基本信息(SPU)
            $this->updateMerchandise($merchandiseId, $params);
            // 更新商品属性和属性值(SPU)
            $this->updateMerchandiseAttributeValue($merchandiseId, $params);
            // 更新商品对应单品(SKU)
            $this->updateMerchandiseItems($merchandiseId, $params);

            Db::commit();

        } catch (throwable $throwable) {
            Db::rollBack();
            Log::error("update_merchandise_error", ['params' => $params, "message" => $throwable->getMessage()]);
            throw $throwable;
        }

        return ['merchandise_id' => $merchandiseId];
    }

    /**
     * 获取商品属性和属性值关联关系
     *
     * @param $merchandiseId
     *
     * @return array
     */
    protected function getMerchandiseAttributeValueAssociatedList($merchandiseId)
    {
        // 商品属性列表
        $merchandiseAttributeList = $this->MerchandiseAttributeService->getMerchandiseAttributeList(['merchandise_id' => $merchandiseId],
            [], ['id', 'merchandise_id', 'attribute_id']);

        $attributeIds  = array_column($merchandiseAttributeList, 'attribute_id');
        $attributeList = $this->AttributeService->getAttributeList([['id', "IN", $attributeIds]], [], ['id', 'name']);

        foreach ($merchandiseAttributeList as &$merchandiseAttribute) {
            foreach ($attributeList as $attribute) {
                if ($merchandiseAttribute['attribute_id'] == $attribute['id']) {
                    $merchandiseAttribute['name'] = $attribute['name'];
                }
            }
        }
        unset($merchandiseAttribute);
        $merchandiseInfo['attribute_list'] = $merchandiseAttributeList;

        $merchandiseAttributeValueList = $this->MerchandiseAttributeValueService->getMerchandiseAttributeValueList(['merchandise_id' => $merchandiseId]);
        $attributeValueIds             = array_column($merchandiseAttributeValueList, 'attribute_value_id');
        $attributeValueList            = $this->AttributeValueService->getAttributeValueList([
            [
                'id',
                "IN",
                $attributeValueIds
            ]
        ], [], ['id', 'attribute_id', 'value']);

        // 商品属性列表
        $merchandiseInfo['attribute_value_list'] = $attributeValueList;

        $itemsMerchandiseAttributeValueList = [];
        foreach ($merchandiseAttributeValueList as &$merchandiseAttributeValue) {
            foreach ($attributeValueList as $attributeValue) {
                if ($merchandiseAttributeValue['attribute_value_id'] == $attributeValue['id']) {
                    $merchandiseAttributeValue['value'] = $attributeValue['value'];
                }
            }
        }
        unset($merchandiseAttributeValue);

        // 商品属性和属性值关系列表
        foreach ($merchandiseAttributeList as $merchandiseAttribute) {
            foreach ($merchandiseAttributeValueList as $merchandiseAttributeValue) {
                if ($merchandiseAttributeValue['attribute_id'] == $merchandiseAttribute['attribute_id']) {
                    $itemsMerchandiseAttributeValueList[$merchandiseAttribute['attribute_id']][] = $merchandiseAttributeValue['attribute_value_id'];
                }
            }
        }

        $merchandiseAttributeValueAssociatedList = [];
        foreach ($itemsMerchandiseAttributeValueList as $attributeId => $itemsMerchandiseAttributeValue) {
            $name                                    = "";
            $merchandiseAttributeValueAssociatedData = [];

            $merchandiseAttributeValueAssociatedData['attribute_id'] = $attributeId;
            foreach ($merchandiseAttributeList as $merchandiseAttribute) {
                if ($attributeId == $merchandiseAttribute['attribute_id']) {
                    $name = $merchandiseAttribute['name'];
                }
            }
            $merchandiseAttributeValueAssociatedData['name'] = $name;

            foreach ($itemsMerchandiseAttributeValue as $valueItem) {
                foreach ($attributeValueList as $attributeValue) {
                    if ($attributeValue['id'] == $valueItem) {
                        $merchandiseAttributeValueAssociatedData['values'][] = $attributeValue;
                    }
                }
            }
            $merchandiseAttributeValueAssociatedList[] = $merchandiseAttributeValueAssociatedData;
        }

        return $merchandiseAttributeValueAssociatedList;

    }

    /**
     * 获取商品商品单品列表
     *
     * @param $merchandiseItemList
     * @param $merchandiseItemId
     *
     * @return mixed
     */
    protected function formatMerchandiseItemList($merchandiseItemList, $merchandiseItemId)
    {

        foreach ($merchandiseItemList as &$merchandiseItem) {

            $itemsAttributeIds      = explode(",", $merchandiseItem['attribute_ids']);
            $itemsAttributeValueIds = explode(",", $merchandiseItem['attribute_value_ids']);

            foreach ($itemsAttributeIds as $k => $itemsAttributeId) {
                $merchandiseItem['item_attribute_value'][$itemsAttributeId] = $itemsAttributeValueIds[$k];
            }
            $merchandiseItem['item_attribute_value_ids'] = implode(',', $merchandiseItem['item_attribute_value']);

            if ($merchandiseItem['id'] == $merchandiseItemId) {
                $merchandiseItem['is_checked'] = 1;
            } else {
                $merchandiseItem['is_checked'] = 0;
            }

        }
        unset($merchandiseItem);

        return $merchandiseItemList;

    }

    /**
     * 获取选中的属性值
     *
     * @param $merchandiseItemList
     *
     * @return array
     */
    protected function getCheckedAttributeValue($merchandiseItemList)
    {
        $itemCheckedAttributeValue = [];
        foreach ($merchandiseItemList as $merchandiseItem) {
            if ($merchandiseItem['is_checked'] == 1) {
                $itemCheckedAttributeValue = $merchandiseItem['item_attribute_value'];
            }
        }

        return $itemCheckedAttributeValue;
    }

    /**
     *  选中的属性值
     *
     * @param $merchandiseAttributeValueAssociatedList
     * @param $itemCheckedAttributeValue
     */
    protected function doCheckedAttributeValue(&$merchandiseAttributeValueAssociatedList, $itemCheckedAttributeValue)
    {
        // 处理默认属性
        foreach ($merchandiseAttributeValueAssociatedList as $key => $attributeValueItemValues) {
            foreach ($attributeValueItemValues['values'] as $k => $attributeValueItemValue) {
                if (in_array($attributeValueItemValue['id'], $itemCheckedAttributeValue)) {
                    $merchandiseAttributeValueAssociatedList[$key]['values'][$k]['is_checked'] = 1;
                } else {
                    $attributeValueItemValues[$key]['values'][$k]['is_checked'] = 0;
                }
            }
        }
    }

    /**
     * 获取参数中笛卡尔积对应的值
     *
     * @param array  $params
     * @param string $key
     *
     * @return array|mixed
     */
    protected function getParamsCartesian(array $params, string $key)
    {
        if (isset($params[$key])) {
            return $params[$key];
        }

        return [];
    }

    /**
     * 根据属性值获取属性id数组
     *
     * @param $attributeValueList
     * @param $combinationAttributeValueData
     *
     * @return array
     */
    protected function getAttributeIdFromCombinations(array $attributeValueList, array $combinationAttributeValueData)
    {
        $attributeIds = [];
        foreach ($combinationAttributeValueData as $combinationAttributeValue) {
            foreach ($attributeValueList as $attributeValue) {
                if ($combinationAttributeValue == $attributeValue['value']) {
                    $attributeIds[] = $attributeValue['attribute_id'];

                }
            }
        }

        return $attributeIds;
    }


    /**
     * 创建商品和商品属性和属性值
     *
     *
     * @param array $params
     *
     * @return mixed
     */
    protected function createMerchandise(array $params)
    {
        $merchandiseData = ['name' => $params['name'], 'introduction' => $params['introduction']];
        $result          = $this->MerchandiseService->create($merchandiseData);

        return (int)$result['id'];
    }

    /**
     * 创建商品属性和属性值信息
     *
     * @param $merchandiseId
     * @param $params
     */
    protected function createMerchandiseAttributeValue($merchandiseId, $params)
    {
        // 创建商品属性和商品属性值
        $attributes = array_keys($params['item_attribute_value']);

        $formattedAttributes = [];
        foreach ($attributes as $attribute) {

            $formattedAttribute = ["merchandise_id" => $merchandiseId, "attribute_id" => $attribute];
            // 创建商品属性
            $merchandiseAttributeResult = $this->MerchandiseAttributeService->create($formattedAttribute);
            $formattedAttribute['id']   = $merchandiseAttributeResult['id'];
            $formattedAttributes[]      = $formattedAttribute;
            $formattedAttributeValues   = [];
            // 创建商品属性属性值
            foreach ($params['item_attribute_value'][$attribute] as $attributeValue) {
                $formattedAttributeValue         = [
                    "merchandise_id"     => $formattedAttribute['merchandise_id'],
                    "attribute_id"       => $formattedAttribute['attribute_id'],
                    "attribute_value_id" => $attributeValue
                ];
                $merchandiseAttributeValueResult = $this->MerchandiseAttributeValueService->create($formattedAttributeValue);
                $formattedAttributeValue['id']   = $merchandiseAttributeValueResult['id'];
                $formattedAttributeValues[]      = $formattedAttributeValue;
            }

        }
    }

    /**
     * 创建商品单品
     *
     * @param int   $merchandiseId
     * @param array $params
     */
    protected function createMerchandiseItems(int $merchandiseId, array $params)
    {
        $attributeValues            = array_values($params['item_attribute_value']);
        $attributeValueCombinations = cartesian($attributeValues);

        foreach ($attributeValueCombinations as $attributeValueCombination) {

            $combinationAttributeValueData = explode(',', $attributeValueCombination);
            $attributeValueList            = $this->AttributeValueService->getAttributeValueList([
                [
                    "id",
                    "IN",
                    $combinationAttributeValueData
                ]
            ]);

            $attributeValueNameList = array_column($attributeValueList, 'value');
            $itemName               = $params['name'] . " " . implode(" ", $attributeValueNameList);
            $item                   = ["merchandise_id" => $merchandiseId, "name" => $itemName];

            $itemCartesianInfo = $this->getParamsCartesian($params['items'], $attributeValueCombination);

            $item['image']               = $itemCartesianInfo['image'];
            $item['merchandise_no']      = $itemCartesianInfo['merchandise_no'];
            $item['storage']             = $itemCartesianInfo['storage'];
            $item['attribute_value_ids'] = $attributeValueCombination;
            $item['attribute_ids']       = implode(",",
                $this->getAttributeIdFromCombinations($attributeValueList, $combinationAttributeValueData));
            $itemResult                  = $this->MerchandiseItemService->create($item);
            $itemId                      = $itemResult['id'];
            $item['id']                  = $itemId;

            foreach ($attributeValueList as $attributeValue) {

                // 创建SKU属性
                $itemAttribute = [
                    'merchandise_id' => $merchandiseId,
                    'item_id'        => $itemId,
                    'attribute_id'   => $attributeValue['attribute_id']
                ];

                $ItemAttributeResult = $this->MerchandiseItemAttributeService->create($itemAttribute);
                $itemAttributeId     = $ItemAttributeResult['id'];
                $itemAttribute['id'] = $itemAttributeId;

                // 创建SKU属性属性值
                $itemAttributeValue = [
                    'merchandise_id'     => $merchandiseId,
                    'item_id'            => $itemId,
                    'attribute_id'       => $attributeValue['attribute_id'],
                    'attribute_value_id' => $attributeValue['id']
                ];

                $result                   = $this->MerchandiseItemAttributeValueService->create($itemAttributeValue);
                $itemAttributeValue['id'] = $result['id'];

            }
        }
    }


    /**
     * 扩展验证创建参数
     *
     * @param array $params
     *
     * @return array
     */
    protected function validateExtendedCreateParams(array $params)
    {
        foreach ($params['item_attribute_value'] as $attributeId => $attributeValue) {
            if (empty($attributeValue)) {
                throw new BusinessException(BusinessErrorCode::MERCHANDISE_ATTRIBUTE_VALUE_ERROR);
            }
        }
        return $params;
    }

    /**
     * 扩展验证更新参数
     *
     * @param array $params
     *
     * @return array
     */
    protected function validateExtendedUpdateParams(array $params, $merchandiseAttributeList)
    {

        $attributeIds           = array_column($merchandiseAttributeList, 'attribute_id');
        $attributeIdsFromClient = array_keys($params['item_attribute_value']);

        foreach ($params['item_attribute_value'] as $attributeId => $attributeValue) {
            if (empty($attributeValue)) {
                throw new BusinessException(BusinessErrorCode::MERCHANDISE_ATTRIBUTE_VALUE_ERROR);
            }
        }

        $intersect = array_intersect($attributeIds, $attributeIdsFromClient);
        if ($intersect == $attributeIds && $intersect == $attributeIdsFromClient) {
            return $params;
        }

        throw new BusinessException(BusinessErrorCode::MERCHANDISE_ATTRIBUTE_ERROR);
    }

    /**
     * 更新商品基本属性信息
     *
     *
     * @param       $merchandiseId
     * @param array $params
     */
    protected function updateMerchandise($merchandiseId, array $params)
    {
        $merchandiseData = [
            'name'         => $params['name'],
            'introduction' => $params['introduction'],
            'id'           => $merchandiseId
        ];
        $this->MerchandiseService->update($merchandiseData);
    }

    /**
     * 跟新商品属性和属性值
     *
     * @param $merchandiseId
     * @param $params
     */
    protected function updateMerchandiseAttributeValue($merchandiseId, $params)
    {
        $merchandiseAttributeValueList = $this->MerchandiseAttributeValueService->getMerchandiseAttributeValueList(['merchandise_id' => $merchandiseId],
            [], ['merchandise_id', 'attribute_id', 'attribute_value_id']);

        // 创建商品属性和商品属性值
        $attributeIds = array_keys($params['item_attribute_value']);
        foreach ($attributeIds as $attributeId) {
            $formattedAttributeValues = [];
            // 创建商品属性属性值
            foreach ($params['item_attribute_value'][$attributeId] as $attributeValue) {

                $formattedAttributeValue = [
                    "merchandise_id"     => $merchandiseId,
                    "attribute_id"       => $attributeId,
                    "attribute_value_id" => $attributeValue
                ];
                if (!in_array($formattedAttributeValue, $merchandiseAttributeValueList)) {
                    $result                        = $this->MerchandiseAttributeValueService->create($formattedAttributeValue);
                    $formattedAttributeValue['id'] = $result['id'];
                }

                $formattedAttributeValues[] = $formattedAttributeValue;
            }
        }
    }

    /**
     * 更新商品单品信息
     *
     * @param       $merchandiseId
     * @param array $params
     */
    protected function updateMerchandiseItems($merchandiseId, array $params)
    {
        $attributeValues            = array_values($params['item_attribute_value']);
        $attributeValueCombinations = cartesian($attributeValues);
        $merchandiseItemList        = $this->MerchandiseItemService->getMerchandiseItemList(['merchandise_id' => $merchandiseId]);
        $attributeValueIdsList = array_column($merchandiseItemList, "attribute_value_ids");

        // 新增
        $createAttributeValueCombinations = [];
        // 更新
        $updateAttributeValueCombinations = [];

        foreach ($attributeValueCombinations as $combination) {
            $create = true;
            foreach ($attributeValueIdsList as $attributeValueIds) {
                if ($combination == $attributeValueIds) {
                    $updateAttributeValueCombinations[] = $combination;
                    $create = false;
                    break;
                }
            }

            if ($create) {
                $createAttributeValueCombinations[] = $combination;
            }
        }

        $deleteAttributeValueCombinations = array_diff($attributeValueIdsList, $updateAttributeValueCombinations);

        foreach ($attributeValueCombinations as $attributeValueCombination) {
            $itemCartesianInfo = $this->getParamsCartesian($params['items'], $attributeValueCombination);
            // 新增
            if (!empty($createAttributeValueCombinations) && in_array($attributeValueCombination,
                    $createAttributeValueCombinations)) {

                $combinationAttributeValueData = explode(',', $attributeValueCombination);
                $attributeValueList            = $this->AttributeValueService->getAttributeValueList([
                    [
                        "id",
                        "IN",
                        $combinationAttributeValueData
                    ]
                ]);

                $attributeValueNameList = array_column($attributeValueList, 'value');
                $itemName               = $params['name'] . " " . implode(" ", $attributeValueNameList);
                $item                   = ["merchandise_id" => $merchandiseId, "name" => $itemName];


                $item['image']               = $itemCartesianInfo['image'];
                $item['merchandise_no']      = $itemCartesianInfo['merchandise_no'];
                $item['storage']             = $itemCartesianInfo['storage'];
                $item['attribute_value_ids'] = $attributeValueCombination;
                $item['attribute_ids']       = implode(",",
                    $this->getAttributeIdFromCombinations($attributeValueList, $combinationAttributeValueData));

                $itemResult = $this->MerchandiseItemService->create($item);
                $itemId     = $itemResult['id'];
                $item['id'] = $itemId;

                foreach ($attributeValueList as $attributeValue) {

                    // 创建SKU属性
                    $itemAttribute = [
                        'merchandise_id' => $merchandiseId,
                        'item_id'        => $itemId,
                        'attribute_id'   => $attributeValue['attribute_id']
                    ];

                    $ItemAttributeResult = $this->MerchandiseItemAttributeService->create($itemAttribute);
                    $itemAttributeId     = $ItemAttributeResult['id'];
                    $itemAttribute['id'] = $itemAttributeId;

                    // 创建SKU属性属性值
                    $itemAttributeValue = [
                        'merchandise_id'     => $merchandiseId,
                        'item_id'            => $itemId,
                        'attribute_id'       => $attributeValue['attribute_id'],
                        'attribute_value_id' => $attributeValue['id']
                    ];

                    $result                   = $this->MerchandiseItemAttributeValueService->create($itemAttributeValue);
                    $itemAttributeValue['id'] = $result['id'];

                }
            }

            // 更新
            if (!empty($updateAttributeValueCombinations) && in_array($attributeValueCombination,
                    $updateAttributeValueCombinations)) {

                $item['image']          = $itemCartesianInfo['image'];
                $item['merchandise_no'] = $itemCartesianInfo['merchandise_no'];
                $item['storage']        = $itemCartesianInfo['storage'];

                $condition['merchandise_id']     = $merchandiseId;
                $condition['attribute_value_ids'] = $attributeValueCombination;

                $this->MerchandiseItemService->updateByCondition($item, $condition);
            }
        }

        // 删除
        if (!empty($deleteAttributeValueCombinations)) {

            $merchandiseItemList = $this->MerchandiseItemService->getMerchandiseItemList([['attribute_value_ids', 'IN', $deleteAttributeValueCombinations]]);
            $merchandiseItemIds = array_column($merchandiseItemList, 'id');

            $condition['merchandise_id'] = $merchandiseId;
            $condition[] = ['id' ,'IN', $merchandiseItemIds];
            $this->MerchandiseItemService->deleteByCondition($condition);

            $AttributeCondition['merchandise_id'] = $merchandiseId;
            $AttributeCondition[] = ['item_id' ,'IN', $merchandiseItemIds];
            $this->MerchandiseItemAttributeService->deleteByCondition($AttributeCondition);

            $AttributeValueCondition['merchandise_id'] = $merchandiseId;
            $AttributeValueCondition[] = ['item_id' ,'IN', $merchandiseItemIds];
            $this->MerchandiseItemAttributeValueService->deleteByCondition($AttributeCondition);
        }

    }

    /**
     * 商品（SPU）上下架
     *
     * @param $params
     *
     * @return mixed
     */
    public function state($params)
    {
        $result = $this->MerchandiseService->update($params);
        return $result;
    }

    /**
     * 商品单品（SKU）上下架
     *
     * @param $params
     *
     * @return mixed
     */
    public function itemState($params)
    {
        $result = $this->MerchandiseItemService->update($params);
        return $result;
    }

}
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
use Hyperf\DbConnection\Db;

// TODO : 长业务逻辑代码拆分解决商品图片和商品码和sku关系的问题
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
     * 创建商品
     *
     * @param $params
     *
     * @return array
     */
    public function create($params)
    {
        $merchandiseId = 0;
        try {
            Db::beginTransaction();
            // 创建商品(SPU)
            $merchandiseData   = ['name' => $params['name'], 'introduction' => $params['introduction']];
            $merchandiseResult = $this->MerchandiseService->create($merchandiseData);

            // 创建商品属性和商品属性值
            $merchandiseId     = $merchandiseResult['id'];
            $attributes        = array_keys($params['item_attribute_value']);

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

            $attributeValues            = array_values($params['item_attribute_value']);
            $attributeValueCombinations = cartesian($attributeValues);

            foreach ($attributeValueCombinations as $attributeValueCombination) {
                // 创建单品(SKU)
                $attributeValueList     = $this->AttributeValueService->getAttributeValueList([
                    [
                        "id",
                        "IN",
                        explode(',', $attributeValueCombination)
                    ]
                ]);
                $attributeValueNameList = array_column($attributeValueList, 'value');
                $itemName               = $params['name'] . " " . implode(" ", $attributeValueNameList);
                $item                   = ["merchandise_id" => $merchandiseId, "name" => $itemName];
                $itemResult             = $this->MerchandiseItemService->create($item);
                $itemId                 = $itemResult['id'];
                $item['id']             = $itemId;

                foreach ($attributeValueList as $attributeValue) {
                    // 创建SKU属性
                    $itemAttribute       = [
                        'merchandise_id' => $merchandiseId,
                        'item_id'        => $itemId,
                        'attribute_id'   => $attributeValue['attribute_id']
                    ];
                    $ItemAttributeResult = $this->MerchandiseItemAttributeService->create($itemAttribute);
                    $itemAttributeId     = $ItemAttributeResult['id'];
                    $itemAttribute['id'] = $itemAttributeId;
                    // 创建SKU属性属性值
                    $itemAttributeValue       = [
                        'merchandise_id'     => $merchandiseId,
                        'item_id'            => $itemId,
                        'attribute_id'       => $attributeValue['attribute_id'],
                        'attribute_value_id' => $attributeValue['id']
                    ];
                    $result                   = $this->MerchandiseItemAttributeValueService->create($itemAttributeValue);
                    $itemAttributeValue['id'] = $result['id'];
                }
            }

            Db::commit();

        } catch (throwable $throwable) {
            Db::rollBack();
        }

        return ['merchandise_id' => $merchandiseId];
    }

    /**
     * 获取商品详情SPU 和 SKU列表
     *
     * @param $params
     *
     * @return mixed
     */
    public function get($params)
    {
        $merchandiseId       = $params['merchandise_id'];

        $merchandiseInfo     = $this->MerchandiseService->get(['id' => $merchandiseId],
            ['id', 'name', 'introduction'])[0];

        $merchandiseItemList = $this->MerchandiseItemService->getMerchandiseItemList(['merchandise_id' => $merchandiseId],
            ['orderByRaw' => 'id asc'], ['id', 'merchandise_id', 'merchandise_no', 'storage', 'name', 'image']);

        $merchandiseItemId   = $params['item_id'] ?? $merchandiseItemList[0]['id'];
        $merchandiseInfo['item_id'] = $merchandiseItemId;

        // 商品属性列表
        $merchandiseAttributeList = $this->MerchandiseAttributeService->getMerchandiseAttributeList(['merchandise_id' => $merchandiseId],
            [], ['id', 'merchandise_id', 'attribute_id']);

        $attributeIds  = array_column($merchandiseAttributeList, 'attribute_id');
        $attributeList = $this->AttributeService->getAttributeList([['id', "IN", $attributeIds]], [],
            ['id', 'name']);

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
        $attributeValueIds  = array_column($merchandiseAttributeValueList, 'attribute_value_id');
        $attributeValueList = $this->AttributeValueService->getAttributeValueList([
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
            $merchandiseAttributeValueAssociatedData = [];

            $merchandiseAttributeValueAssociatedData['attribute_id'] = $attributeId;
            $name                                                    = "";
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
        $merchandiseInfo['attribute_value_associated_List'] = $merchandiseAttributeValueAssociatedList;

        // 单品信息(item)
        $merchandiseItemAttributeList      = $this->MerchandiseItemAttributeService->getMerchandiseItemAttributeList(['merchandise_id' => $merchandiseId]);
        $merchandiseItemAttributeValueList = $this->MerchandiseItemAttributeValueService->getMerchandiseItemAttributeValueList(['merchandise_id' => $merchandiseId]);

        foreach ($merchandiseItemList as &$merchandiseItem) {
            foreach ($merchandiseItemAttributeList as $merchandiseItemAttribute) {
                if ($merchandiseItem['id'] == $merchandiseItemAttribute['item_id']) {
                    foreach ($merchandiseItemAttributeValueList as $merchandiseItemAttributeValue) {
                        if ($merchandiseItemAttributeValue['item_id'] == $merchandiseItemAttribute['item_id'] && $merchandiseItemAttribute['attribute_id'] == $merchandiseItemAttributeValue['attribute_id']) {
                            $merchandiseItem['item_attribute_value'][$merchandiseItemAttribute['attribute_id']] = $merchandiseItemAttributeValue['attribute_value_id'];
                        }

                    }
                    $merchandiseItem['item_attribute_value_ids'] = implode(',',
                        $merchandiseItem['item_attribute_value']);

                }
            }

        }
        unset($merchandiseItem);
        // 处理默认选中的问题 TODO

        $merchandiseInfo['item_list'] = $merchandiseItemList;

        return $merchandiseInfo;
    }

    /**
     * // TODO 商品去重保存
     * 更新商品
     *
     * @param $params
     */
    public function update($params)
    {

    }


}
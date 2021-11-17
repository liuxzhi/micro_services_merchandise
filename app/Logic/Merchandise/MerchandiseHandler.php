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
            $merchandiseId     = $merchandiseResult['id'];
            $attributes        = array_keys($params['item_attribute_value']);


            // 创建商品属性和商品属性值
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
                $attributeValueList     = $this->AttributeValueService->getAttributeValueListByCondition([
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
     * 获取商品
     *
     * @param $params
     *
     * @return mixed
     */
    public function get($params)
    {
        $merchandiseId     = $params['merchandise_id'];
        $merchandiseItemId = $params['merchandise_item_id'];

        $merchandiseInfo = $this->MerchandiseService->get(['id' => $merchandiseId]);

        $merchandiseAttributeList = $this->MerchandiseAttributeService->getMerchandiseAttributeListByCondition(['merchandise_id' => $merchandiseId],
            [], ['id', 'merchandise_id', 'attribute_id']);
        $attributeIds             = array_column($merchandiseAttributeList, 'attribute_id');
        $attributeList            = $this->AttributeService->getAttributeListByCondition([['id', "IN", $attributeIds]]);

        foreach ($merchandiseAttributeList as &$merchandiseAttribute) {
            foreach ($attributeList as $attribute) {
                if ($merchandiseAttribute['attribute_id'] == $attribute['id']) {
                    $merchandiseAttribute['name'] = $attribute['name'];
                }
            }
        }
        unset($merchandiseAttribute);

        $merchandiseInfo['attribute_list'] = $merchandiseAttributeList;

        $itemMerchandiseAttributeValueList = [];
        $merchandiseAttributeValueList = $this->MerchandiseAttributeValueService->getMerchandiseAttributeValueListByCondition(['merchandise_id' => $merchandiseId]);
        $attributeValueIds = array_column($merchandiseAttributeValueList, 'attribute_value_id');
        $attributeValueList = $this->AttributeValueService->getAttributeValueListByCondition([['id', "IN", $attributeValueIds]]);

        foreach ($merchandiseAttributeValueList as &$merchandiseAttributeValue) {
            foreach ($attributeValueList as $attributeValue) {
                if ($merchandiseAttributeValue['attribute_value_id'] == $attributeValue['id']) {
                    $merchandiseAttributeValue['value'] = $attributeValue['value'];
                }
            }
        }
        unset($merchandiseAttributeValue);

        foreach ($merchandiseAttributeList as $merchandiseAttribute) {
            foreach ($merchandiseAttributeValueList as $merchandiseAttributeValue) {
                if ($merchandiseAttributeValue['attribute_id'] == $merchandiseAttribute['attribute_id']) {
                    $itemMerchandiseAttributeValueList[$merchandiseAttribute['attribute_id']][] = $merchandiseAttributeValue['attribute_value_id'];
                }
            }
        }
        $merchandiseInfo['item_attribute_value_list'] = $itemMerchandiseAttributeValueList;
        return $merchandiseInfo;

        // todo
        $merchandiseItemInfo               = $this->MerchandiseItemService->get($merchandiseItemId);
        $merchandiseItemAttributeList      = $this->MerchandiseItemAttributeService->getMerchandiseAttributeValueListByCondition(['merchandise_id' => $merchandiseId]);
        $merchandiseItemAttributeValueList = $this->MerchandiseItemAttributeService->getMerchandiseAttributeValueListByCondition(['merchandise_id' => $merchandiseId]);


    }

    /**
     * 更新商品
     *
     * @param $params
     */
    public function update($params)
    {

    }


}
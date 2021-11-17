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
     * @return array
     */
	public function create($params)
	{

	    try {
            // 创建商品(SPU)
            $merchandiseData = ['name' => $params['name'], 'introduction' => $params['introduction']];
            $merchandiseResult = $this->MerchandiseService->create($merchandiseData);
            $merchandiseId = $merchandiseResult['id'];
            $attributes = array_keys($params['item_attribute_value']);

            Db::beginTransaction();
            // 创建商品属性和商品属性值
            $formattedAttributes = [];
            foreach ($attributes as $attribute) {
                $formattedAttribute = ["merchandise_id" => $merchandiseId,"attribute_id" => $attribute];
                // 创建商品属性
                $merchandiseAttributeResult = $this->MerchandiseAttributeService->create($formattedAttribute);
                $formattedAttribute['id'] = $merchandiseAttributeResult['id'];
                $formattedAttributes[] = $formattedAttribute;
                $formattedAttributeValues = [];
                // 创建商品属性属性值
                foreach ($params['item_attribute_value'][$attribute] as $attributeValue) {
                    $formattedAttributeValue = ["merchandise_id" => $formattedAttribute['merchandise_id'], "attribute_id" => $formattedAttribute['attribute_id'], "attribute_value_id" => $attributeValue];
                    $merchandiseAttributeValueResult = $this->MerchandiseAttributeValueService->create($formattedAttributeValue);
                    $formattedAttributeValue['id'] = $merchandiseAttributeValueResult['id'];
                    $formattedAttributeValues[] =  $formattedAttributeValue;
                }
            }

            $attributeValues = array_values($params['item_attribute_value']);
            $attributeValueCombinations = cartesian($attributeValues);


            foreach ($attributeValueCombinations as $attributeValueCombination) {

                // 创建单品(SKU)
                $attributeValueList = $this->AttributeValueService->getAttributeValueListByCondition([["id", "IN", explode(',' ,$attributeValueCombination)]]);
                $attributeValueNameList = array_column($attributeValueList, 'value');
                $itemName = $params['name']." ".implode(" ", $attributeValueNameList);
                $item = ["merchandise_id" => $merchandiseId, "name" => $itemName];
                $itemResult = $this->MerchandiseItemService->create($item);
                $itemId = $itemResult['id'];
                $item['id'] = $itemId;

                foreach ($attributeValueList as $attributeValue) {
                    // 创建SKU属性
                    $itemAttribute = ['merchandise_id' => $merchandiseId, 'item_id' => $itemId, 'attribute_id' => $attributeValue['attribute_id']];
                    $ItemAttributeResult = $this->MerchandiseItemAttributeService->create($itemAttribute);
                    $itemAttributeId = $ItemAttributeResult['id'];
                    $itemAttribute['id'] = $itemAttributeId;
                    // 创建SKU属性属性值
                    $itemAttributeValue = ['merchandise_id' => $merchandiseId, 'item_id' => $itemId, 'attribute_id' => $attributeValue['attribute_id'], 'attribute_value_id' => $attributeValue['id']];
                    $result = $this->MerchandiseItemAttributeValueService->create($itemAttributeValue);
                    $itemAttributeValue['id'] = $result['id'];
                }
            }

            Db::commit();

        } catch (throwable $throwable) {
            Db::rollBack();
        }


	    return ['11111'];

	}

	/**
	 * 更新商品
	 * @param $params
	 */
	public function update($params)
	{

	}

	/**
	 * 获取商品
	 * @param $params
	 */
	public function get($params)
	{

	}

}
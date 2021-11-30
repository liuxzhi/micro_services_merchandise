<?php
declare(strict_types=1);

namespace App\Request\Merchandise;

trait MerchandiseSceneRequest
{
    /**
     * 验证场景
     * @return array|array[] 场景规则
     */
    protected function scene(): array
    {
        return [
            'create' => ['introduction', 'name', 'item_attribute_value', 'items'],
            'update' => ['id', 'introduction', 'name', 'item_attribute_value', 'items'],
            'get' => ['id'],
            'state' => ['id'],
            'itemState' => ['id'],
        ];
    }

    /**
     * @return string[] 规则
     */
    protected function rules(): array
    {
        return [
            'id' => 'required|integer|min:1|bail',
            'name' => 'required|string|min:1|bail',
            'introduction' => 'required|string|bail',
            'item_attribute_value' => 'required|array|bail',
            'items' => 'required|array|bail',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息.
     */
    protected function messages(): array
    {
        return [
            'id.required' => 'id 必填',
            'id.integer' => 'id 必需为整数',
            'name.required' => 'name 必填',
            'name.min' => 'name 不可为空',
            'name.string' => 'name 必需是字符串类型',
            'introduction.required' => 'introduction 必填',
            'introduction.string' => 'string 必需是字符串类型',
            'item_attribute_value' => 'item_attribute_value 必填',
            'item_attribute_value.array' => 'item_attribute_value 必需是数组',
            'items' => 'items 必填',
            'items.array' => 'array 必需是数组',
        ];
    }

    /**
     * @param $inputs
     * @param $scene
     *
     * @return mixed
     */
    protected function validateExtend(&$inputs, $scene)
    {
        return $inputs;
    }

}
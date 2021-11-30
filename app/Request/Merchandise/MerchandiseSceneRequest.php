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
        return [];
    }

    /**
     * @return string[] 规则
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * 获取已定义验证规则的错误消息.
     */
    protected function messages(): array
    {
        return [];
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
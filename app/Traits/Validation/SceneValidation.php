<?php
declare(strict_types=1);


namespace App\Traits\Validation;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\ValidationException;

/**
 * 场景验证
 *
 * @method array scene() 场景
 * @method array rules() 验证规则
 * @method array messages() 规则错误自定义
 * @method array attributes() 规则属性替换
 * @method void validateExtend(array $inputs, string $scene) 自定义扩展验证 $input为验证数据 $scene为场景名称
 */
trait SceneValidation
{

    /**
     * @param $inputs
     * @param $method
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getPayload($inputs, $method)
    {
        $payload = $this->validated($inputs, $method);

        return (array)$payload;
    }
    /**
     * 获取方法明
     * @param $method
     *
     * @return mixed
     */
    protected function getMethod($method)
    {
        return $method = explode("::", $method)[1] ?? "";
    }

    /**
     * 过滤规则场景化.
     * @param array $rules 过滤规则
     * @param string $scene
     * @return array 返回场景化后的规则
     */
    protected function sceneFormat(array $rules, string $scene): array
    {
        $sceneList = $this->scene();
        $sceneData = $sceneList[$scene] ?? [];

        return array_filter($rules, function ($item) use ($sceneData) {
            return in_array($item, $sceneData);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 场景验证
     *
     * @param array  $inputs
     * @param string $scene
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \TypeError
     */
    protected function validated(array $inputs, string $scene = ''): array
    {
        // 规则
        $rules = [];
        method_exists($this, 'rules') && $rules = $this->sceneFormat($this->rules(), $scene);

        // 自定义错误信息
        $messages = [];
        method_exists($this, 'messages') && $messages = $this->messages();

        // 自定义属性
        $attributes = [];
        method_exists($this, 'attributes') && $attributes = $this->attributes();

        $validator = di(ValidatorFactoryInterface::class);
        $validator = $validator->make($inputs, $rules, $messages, $attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // 自定义扩展验证
        method_exists($this, 'validateExtend') && $this->validateExtend($inputs, $scene);

        return $inputs;
    }
}

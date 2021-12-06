<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;

use App\Logic\Merchandise\MerchandiseHandler;
use App\Constants\ErrorCode;
use App\Helper\Log;
use App\Traits\Validation\SceneValidation;
use App\Request\Merchandise\MerchandiseSceneRequest;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use throwable;

class MerchandiseController extends AbstractController
{

    // 场景值验证
    use SceneValidation;

    // 商品控制器场景验证规则
    use MerchandiseSceneRequest;

    /**
     * @Inject
     * @var MerchandiseHandler
     */
    public $merchandiseHandler;


    /**
     * 创建商品
     *
     * @return array
     * @throws throwable
     */
    public function create() :array
    {
        // 验证商品创建
	    $inputs = $this->request->all();
	    $method = $this->getMethod(__METHOD__);
	    $params = $this->getPayload($inputs, $method);
        Log::info("create_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->create($params));
    }

    /**
     * 更新商品信息
     *
     * @return array
     * @throws throwable
     */
    public function update() :array
    {
        $params = $this->request->all();
        Log::info("update_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->update($params));
    }

    /**
     * 获取商品详情
     *
     * @return array
     */
    public function get() :array
    {
        $params = $this->request->all();
        Log::info("get_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->get($params));
    }


    /**
     * 商品上下架(SPU)
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function state() :array
    {
        $inputs = $this->request->all();
        Log::info("state_params", $inputs);

        $method = $this->getMethod(__METHOD__);
        $params = $this->getPayload($inputs, $method);

        return apiReturn(ErrorCode::SUCCESS, '', ['result' => $this->merchandiseHandler->state($params)]);

    }

    /**
     * 商品单品(SKU)的上下架
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function itemState() :array
    {
        $inputs = $this->request->all();
        Log::info("state_params", $inputs);

        $method = $this->getMethod(__METHOD__);
        $params = $this->getPayload($inputs, $method);

        return apiReturn(ErrorCode::SUCCESS, '', ['result' => $this->merchandiseHandler->itemState($params)]);
    }


	/**
	 * 获取用户列表
	 * @return array
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
    public function list() :array
    {
        $inputs  = $this->request->all();

        $method = $this->getMethod(__METHOD__);
        $params = $this->getPayload($inputs, $method);

        $conditions = $params['conditions'] ?? [];
        $options = $params['options'] ?? [];
        $columns = $params['columns'] ?? [];

        $columns ||  $columns = [
                'merchandise.id AS merchandise_id',
                'merchandise.name',
                'merchandise.introduction',
                'merchandise_item.id AS item_id',
                'merchandise_item.name AS item_name',
                'attribute_ids',
                'attribute_value_ids',
                'storage',
                'image'
            ];


        return apiReturn(ErrorCode::SUCCESS, '', [ 'list' => $this->merchandiseHandler->merchandiseList($conditions, $options, $columns)]);
    }

	/**
	 * 商品绑定业务线
	 * @return array
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
    public function bind() :array
    {

	    $inputs = $this->request->all();
	    $method = $this->getMethod(__METHOD__);
	    $params = $this->getPayload($inputs, $method);

	    Log::info("bind_params", $params);
	    return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->bindBusiness($params));

    }



}

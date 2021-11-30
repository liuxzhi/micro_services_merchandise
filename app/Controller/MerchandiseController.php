<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use App\Logic\Merchandise\MerchandiseHandler;
use App\Constants\ErrorCode;
use App\Helper\Log;
use App\Traits\Validation\SceneValidation;
use App\Request\Merchandise\MerchandiseSceneRequest;

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
     */
    public function create()
    {
        // 验证商品创建
        $params = $this->request->all();
        Log::info("create_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->create($params));
    }

    /**
     * 获取商品详情
     */
    public function get()
    {
        $params = $this->request->all();
        Log::info("get_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->get($params));
    }

    /**
     * 更新商品信息
     */
    public function update()
    {
        $params = $this->request->all();
        Log::info("update_params", $params);
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->update($params));
    }

    /**
     * 商品上下架
     */
    public function state()
    {

    }

    /**
     * 商品单品的上下架
     */
    public function itemState()
    {

    }


    /**
     * 获取商品和单品关联关系列表
     * @return mixed
     */
    public function merchandiseAssociatedMerchandiseItemsList()
    {
        $params  = $this->request->all();

        $columns = [
            'merchandise.id',
            'merchandise.name',
            'merchandise.introduction',
            'merchandise_item.id as item_id',
            'merchandise_item.name as item_name',
            'attribute_ids',
            'attribute_value_ids',
            'storage',
            'image'
        ];

        return $this->merchandiseHandler->getMerchandiseAssociatedMerchandiseItemsList($params, $columns);
    }

}

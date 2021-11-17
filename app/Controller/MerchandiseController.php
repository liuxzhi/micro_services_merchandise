<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use App\Logic\Merchandise\MerchandiseHandler;

class MerchandiseController extends AbstractController
{
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
        return $this->merchandiseHandler->create($params);
    }

    /**
     * 获取商品详情
     */
    public function get()
    {
        $params = $this->request->all();
        $params['merchandise_id'] = 1;
        $params['merchandise_item_id'] = 1;
        return $this->merchandiseHandler->get($params);
    }

}

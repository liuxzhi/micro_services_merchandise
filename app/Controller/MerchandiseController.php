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
        $params["name"] = "Apple 苹果12 iPhone 12 5G手机";
        $params["introduction"] = "Apple 苹果12 iPhone 12 5G手机";
        $params["item_attribute_value"][1] = [1, 2, 3, 4];
        $params["item_attribute_value"][2] = [5, 6, 7, 8, 9, 10];

        return $this->merchandiseHandler->create($params);
    }

}

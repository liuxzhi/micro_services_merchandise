<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use App\Logic\Merchandise\MerchandiseHandler;
use App\Constants\ErrorCode;

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
        $params['items'] = [
            "1,5" => [
                "image" => "1,5",
                "merchandise_no" => "1,5"
            ],
            "1,6" => [
                "image" => "1,6",
                "merchandise_no" => "1,6"
            ],
            "1,7" => [
                "image" => "1,7",
                "merchandise_no" => "1,7"
            ],
            "1,8" => [
                "image" => "1,8",
                "merchandise_no" => "1,8"
            ],
            "1,9" => [
                "image" => "1,9",
                "merchandise_no" => "1,9"
            ],
            "1,10" => [
                "image" => "1,5",
                "merchandise_no" => "1,8"
            ],
            "2,5" => [
                "image" => "2,5",
                "merchandise_no" => "2,5"
            ],
            "2,6" => [
                "image" => "2,6",
                "merchandise_no" => "2,6"
            ],
            "2,7" => [
                "image" => "2,7",
                "merchandise_no" => "2,7"
            ],
            "2,8" => [
                "image" => "2,8",
                "merchandise_no" => "2,8"
            ],
            "2,9" => [
                "image" => "2,9",
                "merchandise_no" => "2,9"
            ],
            "2,10" => [
                "image" => "2,10",
                "merchandise_no" => "2,10"
            ],
            "3,5" => [
                "image" => "3,5",
                "merchandise_no" => "3,5"
            ],
            "3,6" => [
                "image" => "3,6",
                "merchandise_no" => "3,6"
            ],
            "3,7" => [
                "image" => "3,7",
                "merchandise_no" => "3,7"
            ],
            "3,8" => [
                "image" => "3,8",
                "merchandise_no" => "3,8"
            ],
            "3,9" => [
                "image" => "3,9",
                "merchandise_no" => "3,9"
            ],
            "3,10" => [
                "image" => "3,10",
                "merchandise_no" => "3,10"
            ],
            "4,5" => [
                "image" => "4,5",
                "merchandise_no" => "4,5"
            ],
            "4,6" => [
                "image" => "4,6",
                "merchandise_no" => "4,6"
            ],
            "4,7" => [
                "image" => "4,7",
                "merchandise_no" => "4,7"
            ],
            "4,8" => [
                "image" => "4,8",
                "merchandise_no" => "4,8"
            ],
            "4,9" => [
                "image" => "4,9",
                "merchandise_no" => "4,9"
            ],
            "4,10" => [
                "image" => "4,10",
                "merchandise_no" => "4,10"
            ],

        ];
        return apiReturn(ErrorCode::SUCCESS, '', $this->merchandiseHandler->create($params));
    }

    /**
     * 获取商品详情
     */
    public function get()
    {
        $params = $this->request->all();
        return apiReturn(ErrorCode::SUCCESS, '',$this->merchandiseHandler->get($params));
    }

}

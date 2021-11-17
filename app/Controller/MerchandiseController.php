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
		$params = $this->request->all();
		return $this->merchandiseHandler->create($params);
	}
}

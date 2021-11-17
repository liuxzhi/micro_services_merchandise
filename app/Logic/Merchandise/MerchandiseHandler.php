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

class MerchandiseHandler
{

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
	protected $MerchandiseItemServiceInterface;


    /**
     * @Inject
     * @var MerchandiseItemAttributeServiceInterface
     */
    protected $MerchandiseItemAttributeService;


    /**
     * @Inject
     * @var MerchandiseItemAttributeValueServiceInterface
     */
    protected $MerchandiseItemAttributeValueServiceInterface;

	/**
	 * 创建商品
	 * @param $params
	 */
	public function create($params)
	{

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
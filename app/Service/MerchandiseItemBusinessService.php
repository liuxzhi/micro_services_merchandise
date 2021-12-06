<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemBusinessServiceInterface;
use App\Model\MerchandiseItemAttributeValue;
use App\Model\Model;

class MerchandiseItemBusinessService extends AbstractService implements  MerchandiseItemBusinessServiceInterface
{
	/**
	 * 获取数据库操作对象
	 *
	 * @return MerchandiseItemAttributeValue|mixed
	 */
	public function getModelObject() :Model
	{

	}
}
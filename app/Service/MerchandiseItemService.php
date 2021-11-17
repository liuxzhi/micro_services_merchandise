<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemAttributeServiceInterface;

class MerchandiseItemService extends AbstractService implements MerchandiseItemAttributeServiceInterface
{
    public function __construct(){
        parent::__construct();
    }
}
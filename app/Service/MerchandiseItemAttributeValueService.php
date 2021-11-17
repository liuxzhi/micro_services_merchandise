<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemAttributeValueServiceInterface;

class MerchandiseItemAttributeValueService extends AbstractService implements MerchandiseItemAttributeValueServiceInterface
{
    public function __construct(){
        parent::__construct();
    }
}
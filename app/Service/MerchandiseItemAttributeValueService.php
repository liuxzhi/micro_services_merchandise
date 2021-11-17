<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemAttributeValueServiceInterface;

class MerchandiseItemAttributeValueService extends AbstractService implements MerchandiseItemAttributeValueServiceInterface
{
    /**
     * MerchandiseItemAttributeValueService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }
}
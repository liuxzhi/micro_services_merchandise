<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeValueServiceInterface;

class MerchandiseAttributeValueService extends AbstractService implements MerchandiseAttributeValueServiceInterface
{
    /**
     * MerchandiseAttributeValueService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }
}
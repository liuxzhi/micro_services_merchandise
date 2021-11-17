<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeServiceInterface;

class MerchandiseAttributeService extends AbstractService implements MerchandiseAttributeServiceInterface
{
    /**
     * MerchandiseAttributeService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }
}
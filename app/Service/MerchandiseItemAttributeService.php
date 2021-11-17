<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseItemAttributeServiceInterface;

class MerchandiseItemAttributeService extends AbstractService implements MerchandiseItemAttributeServiceInterface
{
    /**
     * MerchandiseItemAttributeService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }
}
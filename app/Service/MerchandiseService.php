<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseServiceInterface;

class MerchandiseService  extends AbstractService implements MerchandiseServiceInterface
{
    /**
     * MerchandiseService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }

}
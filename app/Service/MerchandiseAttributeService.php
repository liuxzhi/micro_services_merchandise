<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\MerchandiseAttributeInterface;

class MerchandiseAttributeService extends AbstractService implements MerchandiseAttributeInterface
{
    public function __construct(){
        parent::__construct();
    }
}
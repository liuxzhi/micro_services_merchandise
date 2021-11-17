<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\MerchandiseItemServiceInterface;

class MerchandiseItemService extends AbstractService implements MerchandiseItemServiceInterface
{
    public function __construct(){
        parent::__construct();
    }
}
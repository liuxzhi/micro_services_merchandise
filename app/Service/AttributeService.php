<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\AttributeServiceInterface;

class AttributeService extends AbstractService implements AttributeServiceInterface
{
    public function __construct(){
        parent::__construct();
    }
}
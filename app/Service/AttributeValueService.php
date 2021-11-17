<?php
declare(strict_types=1);

namespace App\Service;

use App\Contract\AttributeValueServiceInterface;

class AttributeValueService extends AbstractService implements AttributeValueServiceInterface
{
    public function __construct(){
        parent::__construct();
    }
}
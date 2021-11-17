<?php
declare(strict_types=1);

namespace App\Service;
use App\Contract\CategoryServiceInterface;

class CategoryService extends AbstractService implements CategoryServiceInterface
{
    /**
     * CategoryService constructor.
     * @throws \Exception
     */
    public function __construct(){
        parent::__construct();
    }
}
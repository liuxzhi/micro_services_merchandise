<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $attribute_id 
 * @property int $is_prime 
 * @property int $state 
 * @property int $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class MerchandiseAttribute extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'merchandise_attribute';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'attribute_id', 'is_prime', 'state', 'deleted_at', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'attribute_id' => 'integer', 'is_prime' => 'integer', 'state' => 'integer', 'created_at' => 'integer', 'updated_at' => 'integer', 'deleted_at' => 'integer'];

    protected $dateFormat = 'U';
}
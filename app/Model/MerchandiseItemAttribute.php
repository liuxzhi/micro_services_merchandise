<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $merchandise_id 
 * @property int $item_id 
 * @property int $attribute_id 
 * @property int $state 
 * @property int $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class MerchandiseItemAttribute extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'merchandise_item_attribute';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'merchandise_id', 'item_id', 'attribute_id', 'state', 'deleted_at', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'merchandise_id' => 'integer', 'item_id' => 'integer', 'attribute_id' => 'integer', 'state' => 'integer', 'deleted_at' => 'integer', 'updated_at' => 'datetime', 'created_at' => 'datetime'];
}
<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $merchandise_id 
 * @property string $name 
 * @property string $image 
 * @property string $sku_id 
 * @property int $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class MerchandiseItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'merchandise_item';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'merchandise_id', 'storage', 'merchandise_no', 'name', 'image', 'sku_id', 'deleted_at', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'merchandise_id' => 'integer', 'storage' => 'integer', 'merchandise_no' => 'string', 'created_at' => 'integer', 'updated_at' => 'integer', 'deleted_at' => 'integer'];

    protected $dateFormat = 'U';
}
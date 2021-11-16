<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property int $attribute_id 
 * @property string $value 
 * @property int $state 
 * @property int $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class AttributeValue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attribute_value';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'attribute_id', 'value', 'state', 'deleted_at', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'attribute_id' => 'integer', 'state' => 'integer', 'deleted_at' => 'integer', 'updated_at' => 'datetime', 'created_at' => 'datetime'];
}
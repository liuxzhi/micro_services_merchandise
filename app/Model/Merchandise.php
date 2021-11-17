<?php

declare (strict_types=1);
namespace App\Model;

/**
 * @property int $id 
 * @property string $name 
 * @property string $introduction 
 * @property int $state 
 * @property int $sort 
 * @property int $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class Merchandise extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'merchandise';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'introduction', 'state', 'sort', 'deleted_at', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'state' => 'integer', 'sort' => 'integer', 'deleted_at' => 'integer', 'created_at' => 'integer', 'updated_at' => 'integer', 'deleted_at' => 'integer'];

    protected $dateFormat = 'U';
}
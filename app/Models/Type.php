<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Tag(name="type", description="Type"),
 * @OA\Schema(
 *  title="types",
 *  required={"type"},
 *  @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *  @OA\Property(property="type", type="string", example="makeup")
 * )
 */ 
class Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'types';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
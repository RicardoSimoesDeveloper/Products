<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Tag(name="brand", description="Brand"),
 * @OA\Schema(
 *  title="brands",
 *  required={"brand"},
 *  @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *  @OA\Property(property="brand", type="string", example="Rexona")
 * )
 */ 
class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'brands';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'brand'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
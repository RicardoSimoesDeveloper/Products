<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Tag(name="product", description="Product"),
 * @OA\Schema(
 *  title="products",
 *  required={"type", "brand", "price"},
 *  @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *  @OA\Property(property="type", type="integer", example =1),
 *  @OA\Property(property="brand", type="integer", example =1),
 *  @OA\Property(property="description", type="string", example ="description of product"),
 *  @OA\Property(property="price", type="decimal", example ="15.99"),
 *  @OA\Property(property="stock", type="integer", example = 10)
 * )
 */ 
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'type',
        'brand',
        'description',
        'price',
        'stock'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = ['type', 'brand'];
    
    public function type()
    {
        return $this->belongsTo(Type::class, 'type');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand');
    }

    public function getPriceAttribute($value)
    {
        return 'US$ ' . number_format($value, 2, ',', '.');
    }

    public function getStockAttribute($value)
    {
        return str_pad($value, 4, 0, STR_PAD_LEFT);
    }

}
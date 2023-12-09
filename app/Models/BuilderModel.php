<?php

namespace App\Models;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderModel extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'company_name',
        'builder_email',
        'account_type',
        'phone_number',
        'address',
        'abn',
        'brand_id',
        'gate',
    ];
    protected $table = 'admin_builders';

    /**
     * Get the brand.
     */

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}

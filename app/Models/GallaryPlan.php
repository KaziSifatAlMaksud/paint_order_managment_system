<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GallaryPlan extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'job_id',
        'img_url',
        'order'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $fillable = [
        'facility_id',
        'name',
        'path',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}

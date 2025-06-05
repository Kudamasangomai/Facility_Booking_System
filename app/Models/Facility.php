<?php

namespace App\Models;

use App\Enum\AvailabilityStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

// #[ApiResource]
#[ApiResource(
    uriTemplate: 'v1/facilities',
    operations: [

        new GetCollection(),
        new Get(uriTemplate: 'v1/facilities/{id}'),
        new Post(),
        new Put(uriTemplate: 'v1/facilities/{id}'),
        new Delete(uriTemplate: 'v1/facilities/{id}'),
    ]
)]
class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'specialnote',
        'price',
        'images',
        'status',
        'user_id'
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

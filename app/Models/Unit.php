<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'latitude',
        'longitude',
        'parent_id',
    ];

    protected $appends = ['location'];

    public function parent():BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children():HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function allChildren():HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function getLocationAttribute():array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }
}

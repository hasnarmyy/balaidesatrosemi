<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'offices';
    protected $primaryKey = 'id_office';
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'geofence',
        'active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'active' => 'boolean',
        'geofence' => 'array',
    ];

    /**
     * Get active office
     */
    public static function getActiveOffice()
    {
        return self::where('active', true)->first();
    }

    /**
     * Calculate distance from coordinates using Haversine formula
     */
    public function calculateDistance($latitude, $longitude)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad($this->latitude);
        $lat2 = deg2rad($latitude);
        $deltaLat = deg2rad($latitude - $this->latitude);
        $deltaLong = deg2rad($longitude - $this->longitude);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($deltaLong / 2) * sin($deltaLong / 2);
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }

    /**
     * Check if coordinates are within office radius
     */
    public function isWithinRadius($latitude, $longitude)
    {
        $distance = $this->calculateDistance($latitude, $longitude);
        return $distance <= $this->radius_meters;
    }
}

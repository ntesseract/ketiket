<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    /**
     * Search for a location using Nominatim API (OpenStreetMap)
     */
    public function searchLocation(string $query, int $limit = 5)
    {
        $cacheKey = 'location_search_' . md5($query . $limit);
        
        return Cache::remember($cacheKey, 86400, function () use ($query, $limit) {
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => $limit,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return [];
        });
    }
    
    /**
     * Get location details by coordinates
     */
    public function getLocationDetails(float $latitude, float $longitude)
    {
        $cacheKey = 'location_details_' . md5($latitude . $longitude);
        
        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude) {
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }
    
    /**
     * Calculate distance between two coordinates (in kilometers)
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        
        return $miles * 1.609344; // Convert miles to kilometers
    }
    
    /**
     * Get nearby attractions
     */
    public function getNearbyAttractions(float $latitude, float $longitude, float $radius = 5.0)
    {
        $cacheKey = 'nearby_attractions_' . md5($latitude . $longitude . $radius);
        
        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude, $radius) {
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => 'tourism',
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'radius' => $radius * 1000, // Convert to meters
                'limit' => 10,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return [];
        });
    }
    
    /**
     * Generate a static map image URL
     */
    public function getStaticMapUrl(float $latitude, float $longitude, int $zoom = 14, int $width = 600, int $height = 400)
    {
        return "https://www.openstreetmap.org/export/embed.html?bbox=" . 
            ($longitude - 0.01) . "%2C" . 
            ($latitude - 0.01) . "%2C" . 
            ($longitude + 0.01) . "%2C" . 
            ($latitude + 0.01) . 
            "&amp;layer=mapnik&amp;marker=" . $latitude . "%2C" . $longitude;
    }
    
    /**
     * Get directions between two points
     * Note: This uses OSRM (Open Source Routing Machine) - a free routing service
     */
    public function getDirections(float $startLat, float $startLon, float $endLat, float $endLon)
    {
        $cacheKey = 'directions_' . md5($startLat . $startLon . $endLat . $endLon);
        
        return Cache::remember($cacheKey, 86400, function () use ($startLat, $startLon, $endLat, $endLon) {
            $response = Http::get("https://router.project-osrm.org/route/v1/driving/{$startLon},{$startLat};{$endLon},{$endLat}", [
                'overview' => 'full',
                'geometries' => 'geojson',
                'steps' => 'true',
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }
}
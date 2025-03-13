@extends('layouts.admin')

@section('title', 'Itinerary Paket Wisata')

@section('content')
<div class="space-y-6">
    <!-- Package Info Header -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    @if ($package->image)
                        <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="h-16 w-16 object-cover rounded-lg">
                    @else
                        <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-suitcase text-indigo-400 text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ $package->name }}</h1>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="flex items-center mr-4">
                            <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>
                            <span>{{ $package->duration_days }} Hari</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-tag mr-1 text-indigo-500"></i>
                            <span>Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.packages.show', $package->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Overview Map -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Peta Rute Perjalanan</h2>
            <div class="h-80 rounded-lg overflow-hidden bg-gray-100">
                @if(count($package->destinations) > 0 && $package->destinations->first()->latitude && $package->destinations->first()->longitude)
                    <div id="map" class="w-full h-full"></div>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <p class="text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i> Belum ada destinasi dengan koordinat yang valid
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Itinerary Timeline -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Jadwal Perjalanan</h2>
            
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute top-0 left-6 bottom-0 w-0.5 bg-indigo-200"></div>
                
                <!-- Timeline Content -->
                <div class="space-y-8">
                    @foreach($itinerary as $day)
                        <div class="relative pl-16" x-data="{ open: false }">
                            <!-- Day Circle -->
                            <div class="absolute top-0 left-0 w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                                Hari {{ $day['day'] }}
                            </div>
                            
                            <!-- Day Content -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2 cursor-pointer" @click="open = !open">
                                    <h3 class="text-lg font-medium text-gray-800">Aktivitas Hari {{ $day['day'] }}</h3>
                                    <button class="text-gray-500 focus:outline-none" aria-label="Toggle day activities">
                                        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                </div>
                                
                                <!-- Activities List -->
                                <div class="overflow-hidden transition-all duration-300 max-h-0" x-ref="activities" x-bind:style="open ? 'max-height: ' + $refs.activities.scrollHeight + 'px' : ''">
                                    <div class="space-y-4 pt-2">
                                        @foreach($day['activities'] as $activity)
                                            <div class="flex items-start pl-5 relative">
                                                <!-- Time Dot -->
                                                <div class="absolute left-0 top-2 w-2 h-2 rounded-full bg-indigo-500"></div>
                                                
                                                <!-- Activity Card -->
                                                <div class="flex-1 ml-3">
                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                                        <div class="mb-2 sm:mb-0">
                                                            <span class="text-sm font-medium text-indigo-600">{{ $activity['time'] }}</span>
                                                            <h4 class="font-medium text-gray-800">{{ $activity['description'] }}</h4>
                                                        </div>
                                                        <div class="px-2 py-1 text-xs rounded-full
                                                            @if($activity['type'] == 'destination') bg-blue-100 text-blue-800
                                                            @elseif($activity['type'] == 'hotel') bg-purple-100 text-purple-800
                                                            @else bg-green-100 text-green-800
                                                            @endif">
                                                            <i class="fas
                                                                @if($activity['type'] == 'destination') fa-map-marker-alt
                                                                @elseif($activity['type'] == 'hotel') fa-hotel
                                                                @else fa-utensils
                                                                @endif mr-1"></i>
                                                            {{ ucfirst($activity['type']) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-3 border-l-2 border-gray-200 pl-3">
                                                        @if($activity['item']->image)
                                                            <img src="{{ Storage::url($activity['item']->image) }}" alt="{{ $activity['item']->name }}" class="h-32 w-full object-cover rounded-lg mb-2">
                                                        @endif
                                                        <p class="text-sm text-gray-600">{{ Str::limit($activity['item']->description, 150) }}</p>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $activity['item']->location }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print & Export Options -->
    <div class="flex space-x-3 justify-end">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-print mr-2"></i> Cetak Itinerary
        </button>
        <a href="#" onclick="alert('Fungsi export sedang dalam pengembangan')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-download mr-2"></i> Export PDF
        </a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Open first day by default
        setTimeout(() => {
            document.querySelector('.relative[x-data]').__x.$data.open = true;
        }, 500);
        
        // Mapbox initialization (if we have coordinates)
        @if(count($package->destinations) > 0 && $package->destinations->first()->latitude && $package->destinations->first()->longitude)
            // Initialize map
            mapboxgl.accessToken = 'YOUR_MAPBOX_API_KEY';
            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [{{ $package->destinations->first()->longitude }}, {{ $package->destinations->first()->latitude }}],
                zoom: 9
            });
            
            // Add markers and route
            map.on('load', function() {
                const coordinates = [];
                const markers = [];
                
                // Add markers for each destination
                @foreach($package->destinations as $index => $destination)
                    @if($destination->latitude && $destination->longitude)
                        coordinates.push([{{ $destination->longitude }}, {{ $destination->latitude }}]);
                        
                        // Create custom marker element
                        const markerEl = document.createElement('div');
                        markerEl.className = 'custom-marker';
                        markerEl.innerHTML = '<div class="marker-number">{{ $index + 1 }}</div>';
                        markerEl.style.width = '30px';
                        markerEl.style.height = '30px';
                        markerEl.style.borderRadius = '50%';
                        markerEl.style.backgroundColor = '#4F46E5';
                        markerEl.style.color = 'white';
                        markerEl.style.textAlign = 'center';
                        markerEl.style.lineHeight = '30px';
                        markerEl.style.fontWeight = 'bold';
                        
                        // Create popup
                        const popup = new mapboxgl.Popup({ offset: 25 })
                            .setHTML('<div><strong>{{ $destination->name }}</strong><p>{{ Str::limit($destination->location, 50) }}</p></div>');
                        
                        // Add marker to map
                        new mapboxgl.Marker(markerEl)
                            .setLngLat([{{ $destination->longitude }}, {{ $destination->latitude }}])
                            .setPopup(popup)
                            .addTo(map);
                    @endif
                @endforeach
                
                // Add route line if we have at least 2 coordinates
                if (coordinates.length >= 2) {
                    map.addSource('route', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': coordinates
                            }
                        }
                    });
                    
                    map.addLayer({
                        'id': 'route',
                        'type': 'line',
                        'source': 'route',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': '#4F46E5',
                            'line-width': 4,
                            'line-opacity': 0.8
                        }
                    });
                    
                    // Fit bounds to show all markers
                    const bounds = coordinates.reduce(function(bounds, coord) {
                        return bounds.extend(coord);
                    }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                    
                    map.fitBounds(bounds, {
                        padding: 50
                    });
                }
            });
        @endif
        
        // Animation for timeline items
        const timelineItems = document.querySelectorAll('.relative.pl-16');
        timelineItems.forEach((item, index) => {
            item.style.opacity = 0;
            item.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                item.style.opacity = 1;
                item.style.transform = 'translateY(0)';
            }, 300 * index);
        });
    });
</script>

<style>
    @media print {
        nav, header, footer, .ml-auto, button {
            display: none !important;
        }
        
        body {
            background-color: white !important;
        }
        
        [x-ref="activities"] {
            max-height: none !important;
            display: block !important;
        }
        
        .relative[x-data] {
            break-inside: avoid;
        }
    }
</style>

<!-- Include Mapbox -->
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js"></script>
@endpush
@endsection
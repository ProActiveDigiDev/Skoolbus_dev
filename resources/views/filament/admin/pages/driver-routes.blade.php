<x-filament-panels::page>
    @livewire('notifications')

    @if(!$routes)
        <div type="info">
            <p>
                No routes yet.
            </p>
        </div>
    @else
        @foreach($routes as $routegroup)
        {{-- @php dd($routegroup) @endphp --}}
        <x-filament::section class="bg-amber-400">
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-row items-center justify-left gap-2">
                    <h3 class="font-bold text-lg">{{ $routegroup[0]['route_info']['from_location']['name'] }}</h3>
                    <sub>To</sub>
                    <h3 class="font-bold text-lg">{{ $routegroup[0]['route_info']['to_location']['name'] }}</h3>
                </div>
                <div>
                    <span class="font-bold">{{ $routegroup[0]['route_info']['timeslot']['departure_time'] }}</span>
                </div>
            </div>
            @foreach ($routegroup as $key => $route)
                {{-- @php dd($route) @endphp --}}
                @component('livewire.rider.rider-card-mini',[
                    'avatar_url' => $route['rider_info']['avatar'],
                    'name' => $route['rider_info']['name'],
                    'surname' => $route['rider_info']['surname'],
                    'school' => $route['rider_info']['school_location']['name'] ?? 'No School Listed',
                        
                ])
                @endcomponent
            @endforeach
        </x-filament::section>
        @endforeach
    @endif

</x-filament-panels::page>

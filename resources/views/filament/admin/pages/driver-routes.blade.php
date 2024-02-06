<x-filament-panels::page>
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
                        <x-filament::section class="mt-2">
                            <div class="flex flex-row items-center justify-between">
                                <div class="flex flex-row items-center justify-left gap-6">
                                    <div class="">
                                        <x-filament::avatar 
                                        src="{{ asset('storage/user_avatars/' . $route['rider_info']['avatar']) }}"
                                        alt="{{ $route['rider_info']['name'] . ' Avatar'}}"
                                        size="w-20 h-20"
                                        />
                                    </div>
                                    <div class="">
                                        <h3 class="text-md"><b>{{ $route['rider_info']['name'] }}</b> {{ $route['rider_info']['surname'] }}</h3>
                                        <span class="text-sm">{{ $route['rider_info']['school_location']['name'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </x-filament::section>
                    @endforeach
                    
                    <div class="flex flex-row items-center justify-center">
                        <x-filament::button outlined
                        wire:click.prevent="confirmRouteCompleted({{ $routegroup[0]['route_info']['id'] }})" 
                        size="sm"
                        color="primary"
                        class="mt-6"
                        >
                            Complete Route
                        </x-filament::button>
                    </div>
        
                </x-filament::section>
            @endforeach
    
        @endif
</x-filament-panels::page>

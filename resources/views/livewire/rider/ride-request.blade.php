@php
    // dd($website_configs);
@endphp

<style>
.brand_color-primary {
    background: {{ $website_configs['site_brand_color_primary'] }};
}
.brand_color-primary-hover:hover {
    background: {{ $website_configs['site_brand_color_primary'] }};
}
.brand_color-primary-asColor {
    color: {{ $website_configs['site_brand_color_primary'] }};
}
.brand_color-primary-asColor-hover:hover {
    color: {{ $website_configs['site_brand_color_primary'] }};
}

.brand_color-secondary {
    background: {{ $website_configs['site_brand_color_secondary'] }};
}
.brand_color-secondary-hover:hover {
    background: {{ $website_configs['site_brand_color_secondary'] }};
}
.brand_color-secondary-asColor {
    color: {{ $website_configs['site_brand_color_secondary'] }};
}
.brand_color-secondary-asColor-hover:hover {
    color: {{ $website_configs['site_brand_color_secondary'] }};
}

.brand_text-primary {
    color: {{ $website_configs['site_brand_text_primary'] }};
}
.brand_text-primary-hover:hover {
    color: {{ $website_configs['site_brand_text_primary'] }};
}
.brand_text-primary-asBg {
    background: {{ $website_configs['site_brand_text_primary'] }};
}
.brand_text-primary-asBg-hover:hover {
    background: {{ $website_configs['site_brand_text_primary'] }};
}

.brand_text-secondary {
    color: {{ $website_configs['site_brand_text_secondary'] }};
}
.brand_text-secondary-hover:hover {
    color: {{ $website_configs['site_brand_text_secondary'] }};
}
.brand_text-secondary-asBg {
    background: {{ $website_configs['site_brand_text_secondary'] }};
}
.brand_text-secondary-asBg-hover:hover {
    background: {{ $website_configs['site_brand_text_secondary'] }};
}

</style>
@livewireStyles
@livewireScripts


<x-layouts.app>

    <div class="flex flex-col items-center justify-center max-h-fit min-h-screen brand_color-primary">
        <div class="flex flex-col items-center justify-center max-w-lg">

            @if ($request_message[0] != 'noRider')
                <div class="rider-info-holder block">
                    <div class="flex items-center justify-center">
                        <h2 class="text-2xl font-bold brand_text-primary">{{ $rider_info['name'] }}</h2>
                    </div>
                    <div class="img-holder flex items-center justify-center py-2">
                        <img class="rider-avatar rounded-full" src="{{ asset('storage/user_avatars/' . $rider_info['avatar']) }}" alt="Rider Avatar" width="200" height="200">
                    </div>
                </div>
            
                @if ($request_message[0] == 'success')
                    <div class="flex flex-col items-center justify-center mx-auto px-4">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-md font-bold brand_text-primary">Requesting a Ride for: </span>
                            <h2 class="text-2xl font-bold brand_text-primary">{{ $route_info['name'] ?? 'No Route' }}</h2>
                            <div class=" brand_text-primary">
                                {{ $route_info['fromLocation']['name'] }}<span class="text-md font-bold"> - </span>{{ $route_info['toLocation']['name'] }}
                            </div>
                            <span class="text-md font-bold brand_text-primary">{{ $route_info['timeslot']['departure_time'] }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center my-4 mx-auto px-4 confirmation-holder">
                        <button id="accept-deny-ride" wire:click.prevent="acceptRequest({{ $request_message[1] == 'success' ? 'Accept' : "Error" }})" class="flex flex-col items-center justify-center py-6 px-4 rounded-lg w-60 brand_color-secondary brand_text-primary-asBg-hover brand_text-primary brand_color-primary-asColor-hover">
                            {{ $request_message[1] == 'success' ? 'Accept' : "Error" }}
                        </button>
                        
                        <button class="flex flex-col items-center justify-center mt-3 py-2 px-4 rounded-lg w-60 brand_color-primary-asText brand_color-secondary-asColor-hover">
                            {{ $request_message[1] = 'success' ? 'Decline' : "Error" }}
                        </button>
                    </div>
                @else
                    <h3 class="text-2xl font-bold brand_text-primary">{{ $request_message[1] }}</h3>
                @endif
            @else
                <h3 class="text-2xl font-bold brand_text-primary">{{ $request_message[1] }}</h3>
            @endif
                
        </div>
        
    </div>

</x-layouts.app>

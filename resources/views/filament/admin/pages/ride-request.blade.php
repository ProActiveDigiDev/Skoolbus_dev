<div>
    <style>
        .brand_color-primary {
            background: {{ $website_configs['site_brand_color_primary'] }};
        }
        .brand_color-primary-hover:hover {
            background: {{ $website_configs['site_brand_color_primary'] }};
        }
        .brand_border-primary {
            border: solid 1px {{ $website_configs['site_brand_color_primary'] }};
        }
        .brand_text-primary {
            color: {{ $website_configs['site_brand_color_primary'] }};
        }
        .brand_text-primary-hover:hover {
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
    
        <div class="flex flex-col items-center justify-center max-h-fit min-h-screen brand_color-primary">
    
            @if ($request_message[0] != 'noRider')
                <div class="w-full">
                    <div name="heading" class="flex items-center justify-center w-full">
                        <h2 class="mx-auto text-2xl font-bold brand_text-primary">{{ $rider_info['name'] . ' ' . $rider_info['surname'] }}</h2>
                    </div>
                
                    <img
                        class="rider-avatar mx-auto"
                        src="{{ asset('storage/user_avatars/' . $rider_info['avatar']) }}"
                        alt="{{ $rider_info['name'] . ' Avatar'}}"
                        class="rounded-full"
                        style="object-fit: cover; object-position: center; width: 150px; height: 150px; border-radius: 50%;"
                    />
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
                        <div class="flex flex-col items-center justify-center mt-6 my-4 mx-auto px-4 confirmation-holder gap-4">
                            @if($request_message[1] == 'success')
    
                                <x-filament::button
                                wire:click.prevent="acceptRequest" 
                                size="xl"
                                color="primary"
                                class="mb-6"
                                >
                                    Accept Request
                                </x-filament::button>
    
                                <x-filament::button outlined
                                wire:click.prevent="rejectRequest"
                                size="sm"
                                color="secondary"
                                class="mt-4"
                                >
                                    Decline
                                </x-filament::button>
    
                            @elseif ($request_message[1] == 'DONE')
                                <h3 class="text-2xl font-bold brand_text-primary">Ride Request Accepted</h3>
                            @elseif ($request_message[1] == 'CANCELLED')
                                <h3 class="text-2xl font-bold brand_text-primary">Ride Request was Cancelled</h3>
    
                                <x-filament::button outlined
                                wire:click.prevent="acceptRequest" 
                                size="sm"
                                color="primary"
                                class="mb-6"
                                >
                                    Accept Request
                                </x-filament::button>
                            @else
                                <h3 class="text-2xl font-bold brand_text-primary">An Error Occured</h3>
                            @endif
                        </div>
    
                    @else
                        <div class="text-center">
                            <h3 class="text-2xl font-bold brand_text-primary">{!! $request_message[1] !!}</h3>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center">
                    <h3 class="text-2xl font-bold brand_text-primary">{!! $request_message[1] !!}</h3>
                </div>
            @endif
    
            @if($emergency_info)
                <x-filament::modal>
                    <x-slot name="trigger" style="position:fixed; bottom:30px; right:30px;">
                        <x-filament::button color="danger">
                            Emergency Info
                        </x-filament::button>
                        <x-slot name="heading">
                            Emergency Info
                        </x-slot>
                    </x-slot>
                
                    {{-- Modal content --}}
                    <div class="flex flex-col items-center justify-center my-6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex flex-col items-center justify-center brand_text-primary">
                                <strong>{{ $emergency_contact['ec_relationship'] }}</strong>
                                {{ $emergency_contact['ec_name'] }} {{ $emergency_contact['ec_surname'] }}
                            </div>
                            <x-filament::link 
                            :href="'tel:' . $emergency_contact['ec_contact_number']"
                            icon="heroicon-m-phone"
                            >
                                <h2 class="text-2xl font-bold brand_text-primary">{{ $emergency_contact['ec_contact_number'] }}</h2>
                            </x-filament::link>
                        </div>
    
                        @if($emergency_info['has_medical_aid'])
                        <x-filament::section 
                        collapsible
                        collapsed
                        class="mt-6">
                            <x-slot name="heading">
                                Medical Aid Info
                            </x-slot>
    
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center justify-center brand_text-primary">
                                    <div>
                                        Medical Aid Name: <strong>{{ $emergency_info['medical_aid_name'] }}</strong>
                                    </div>
                                    <div>
                                        Medical Aid Plan: <strong>{{ $emergency_info['medical_aid_plan'] }}</strong>
                                    </div>
                                    <div>
                                        Main Member: <strong>{{ $emergency_info['medical_aid_main_member_name'] }}</strong>
                                    </div>
                                    <div>
                                        Member Number: <strong>{{ $emergency_info['medical_aid_main_member_number'] }}</strong>
                                    </div>
                                </div>
                                
    
                        </x-filament::section>
                        @endif
                    </div>
                    
    
                    
                </x-filament::modal>
            @endif
        </div>
        
    </div>
    
    
    
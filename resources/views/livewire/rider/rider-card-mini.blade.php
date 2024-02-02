<x-filament::section class="mt-2">

    <x-filament::modal>
        <x-slot name="trigger">
            <div class="flex flex-row items-center justify-left gap-6">
                <div class="">
                    <x-filament::avatar 
                    src="{{ asset('storage/user_avatars/' . $avatar_url) }}"
                    alt="{{ $name . ' Avatar'}}"
                    size="w-20 h-20"
                    />
                </div>
                <div class="">
                    <h3 class="text-md"><b>{{ $name }}</b> {{ $surname }}</h3>
                    <span class="text-sm">{{ $school }}</span>
                </div>
            </div>
            <x-slot name="heading">
                {{ $name . ' ' . $surname }}
            </x-slot>
        </x-slot>
    
        {{-- Modal content --}}
        {{-- <div class="flex flex-col items-center justify-center my-6">
            @if($emergency_info)
            <x-filament::section collapsible collapsed class="mt-6">
                <x-slot name="heading">
                    Medical Aid Info
                </x-slot>
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
        </div>                     --}}
    </x-filament::modal>


</x-filament::section>
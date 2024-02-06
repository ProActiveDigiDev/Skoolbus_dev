@php
    
@endphp

<x-filament-panels::page>   

    <div x-data="{activeTab : $wire.$entangle('activeTab') }">
        <x-filament::tabs label="Content tabs">

            @foreach ($tabNames as $tabName)
                <x-filament::tabs.item
                :active="$activeTab === 'tab_{{ $loop->iteration }}'"
                wire:click="$set('activeTab', 'tab_{{ $loop->iteration }}')"
                >
                {{ $tabName }}
                </x-filament::tabs.item>
                
            @endforeach

            <div class="flex justify-end">
                <x-filament::button wire:click="submit">
                    Save Changes
                </x-filament::button>
            </div>
        
        </x-filament::tabs>

        <div class="tabs-content-holder">

            @foreach ($tabNames as $formName => $tabName)
                @if ($formName == 'riderForm')
                    <div x-show="activeTab == 'tab_{{ $loop->iteration }}'">
                        <x-filament::section>
                            @if(!$this->riders)
                                <div class="flex justify-center">
                                    <h3>No Riders Found</h3>
                                </div>
                            @endif
            
                            <x-filament::modal width="2xl">
                                <x-slot name="trigger">
                                    <div class="block">
                                        @foreach($this->riders as $rider)
                                            <x-filament::button style="margin:0 0 20px 20px;" wire:click="riderFormFill({{ $rider->id }})" outlined>
                                                {{ $rider->name }}
                                            </x-filament::button>
                                        @endforeach

                                    </div>
                                </x-slot>
                                    
                                {{-- Modal content --}}
                                <div>
                                    <form>
                                        {{ $this->riderForm  }}
                                        
                                        
                                        <x-filament::button style="margin-top:20px;" wire:click="riderFormSubmit({{ $rider->id }})" outlined>
                                            Save
                                        </x-filament::button>
                                    </form>
                                </div>
                            </x-filament::modal>
                        </x-filament::section>
                    </div>

                @else
                    <div x-show="activeTab == 'tab_{{ $loop->iteration }}'">
                        <form wire:submit.prevent="submit">
                            @csrf
                            {{ $this->{$formName} }}
                        </form>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
    
</x-filament-panels::page>
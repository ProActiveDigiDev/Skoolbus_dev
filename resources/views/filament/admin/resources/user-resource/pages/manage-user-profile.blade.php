<x-filament-panels::page>   

    <div x-data="{activeTab : $wire.$entangle('activeTab') }">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item
            :active="$activeTab === 'tab_1'"
            wire:click="$set('activeTab', 'tab_1')"
            >
            Account Information
            </x-filament::tabs.item>

            <x-filament::tabs.item
            :active="$activeTab === 'tab_2'"
            wire:click="$set('activeTab', 'tab_2')"
            >
            Profile
            </x-filament::tabs.item>

            <x-filament::tabs.item
            :active="$activeTab === 'tab_3'"
            wire:click="$set('activeTab', 'tab_3')"
            >
            Medical Aid Information
            </x-filament::tabs.item>

            <x-filament::tabs.item
            :active="$activeTab === 'tab_4'"
            wire:click="$set('activeTab', 'tab_4')"
            >
            Emergency Contact Information
            </x-filament::tabs.item>

            <x-filament::tabs.item
            :active="$activeTab === 'tab_5'"
            wire:click="$set('activeTab', 'tab_5')"
            >
            Riders Information
            </x-filament::tabs.item>

            <div class="flex justify-end">
                <x-filament::button wire:click="submit">
                    Save Changes
                </x-filament::button>
            </div>
        
        </x-filament::tabs>

        <div class="tabs-content-holder">

            <div x-show="activeTab == 'tab_1'">
                {{ $this->accountInfolist }} 
            </div>

            <div x-show="activeTab == 'tab_2'">
                <form wire:submit.prevent="submit">
                    @csrf
                    {{ $this->profileForm }}
                </form> 
            </div>
            
            <div x-show="activeTab == 'tab_3'">
                <form wire:submit.prevent="submit">
                    @csrf
                    {{ $this->emergencyInformationForm }}
                </form> 
            </div>
            
            <div x-show="activeTab == 'tab_4'">
                <form wire:submit.prevent="submit">
                    @csrf
                    {{ $this->emergencyContactForm }}
                </form> 
            </div>

            <div x-show="activeTab == 'tab_5'">   
                <x-filament::section>
                
                    @if(!$this->riders)
                        <div class="flex justify-center">
                            <h3>No Riders Found</h3>
                        </div>
                    @endif
    
                    <x-filament::modal>
                        <x-slot name="trigger">
                            @foreach($this->riders as $rider)
                                <x-filament::button wire:click="riderFormFill({{ $rider->id }})" outlined>
                                    {{ $rider->name }} (ID: {{ $rider->id }})
                                </x-filament::button>
                            @endforeach
                        </x-slot>
                            
                        {{-- Modal content --}}
                        <div>
                            <form wire:submit.prevent="riderFormSubmit">
                                {{ $this->riderForm  }}
                                
                                
                                <x-filament::button class="mt-3" type="submit" outlined>
                                    Save
                                    <x-filament::loading-indicator wire:loading class="h-5 w-5" />
                                </x-filament::button>
                            </form>
                        </div>
                    </x-filament::modal>
                </x-filament::section>          
            </div>

        </div>
    </div>
    
</x-filament-panels::page>
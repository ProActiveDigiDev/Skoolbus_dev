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

            <div class="flex justify-end">
                <x-filament::button wire:click="submit" outlined>
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

        </div>
    </div>
    
</x-filament-panels::page>
<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->accountForm }}
        
        <div class="">
            <x-filament::button wire:click="submit" outlined>
                Save
            </x-filament::button>
        </div>
    </form>

    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->profileForm }}
        
        <div class="">
            <x-filament::button wire:click="submit" outlined>
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

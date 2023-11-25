<x-filament-panels::page>

    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->accountForm }}
    </form>

    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->profileForm }}
    </form> 

    <div class="">
        <x-filament::button wire:click="submit" outlined>
            Save
        </x-filament::button>
    </div>
</x-filament-panels::page>

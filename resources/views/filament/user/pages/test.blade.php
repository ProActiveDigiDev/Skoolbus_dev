<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->form }}
        
        <div class="">
            <x-filament::button wire:click="submit" outlined>
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

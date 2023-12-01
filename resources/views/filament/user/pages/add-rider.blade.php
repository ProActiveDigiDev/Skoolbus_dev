<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        @csrf
        {{ $this->form }}
        
        <div class="py-6">
            <x-filament::button wire:click="submit" outlined>
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

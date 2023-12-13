<x-filament-panels::page>
    <form wire:submit.prevent="submit">
       @csrf
        {{ $this->form }}
    
        <button type="submit">Save</button>
    
    </form>
</x-filament-panels::page>

<x-filament-panels::page>
    
    <x-filament::section>
        <x-filament::modal width="2xl" id="credit-purchase-modal">
            <x-slot name="trigger">
                <div class="block">
                    <x-filament::button style="margin:0 0 20px 20px;" outlined>
                        <h4>
                            Buy more credits
                        </h4>
                    </x-filament::button>
                </div>
            </x-slot>
                
            {{-- Modal content --}}
            <div>
                <form wire:submit.prevent="submit">
                    @csrf
                    {{ $this->payfastForm }}
                </form> 

                <div class="w-full flex items-center gap-4">
                    <x-filament::button style="margin-top:20px;" wire:click="submit" outlined>
                        Make payment
                    </x-filament::button>
    
                    <x-filament::button style="margin-top:20px; margin-left:20px" wire:click="closeModal" color="danger" outlined>
                        Cancel
                    </x-filament::button>
                </div>
                    

            </div>
        </x-filament::modal>
    </x-filament::section>
</x-filament-panels::page>

<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <div class="flex flex-row justify-between">
            <div class="flex flex-col justify-center">
                <h2>
                    Credits: <b>{{ $this->user_credits }}</b>
                </h2>
            </div>
            <div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <sub>
                            Credit value: R{{ $this->cost_per_credit * $this->user_credits }}
                        </sub>
                    </div>
                    <div>
                        <sup>
                            Cost per credit: R{{ $this->cost_per_credit }}
                        </sup>
                    </div>
                </div>
            </div>
        </div>        
    </x-filament::section>

</x-filament-widgets::widget>

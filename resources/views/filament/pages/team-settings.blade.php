<x-filament-panels::page>
    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}
        <div class="w-full mt-5 flex justify-end">
        <x-filament::button type="submit" size="sm">
            Save Sattings
        </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>

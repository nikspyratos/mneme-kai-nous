<x-filament::widget>
    <x-filament::card>
        {{ $quote->content }}
        - {{ $quote->author ?? 'Unknown' }}
    </x-filament::card>
</x-filament::widget>

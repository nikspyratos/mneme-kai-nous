<x-filament::widget>
    <x-filament::card>
        @foreach ($schedules as $schedule)
            <p>{{ $schedule->name }}: {{ $schedule->todayTimesFormatted }}</p>
        @endforeach
    </x-filament::card>
</x-filament::widget>

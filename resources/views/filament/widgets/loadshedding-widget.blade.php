<x-filament::widget>
    <x-filament::card>
        @foreach ($schedules as $schedule)
            <p>{{ $schedule->name }}: {{ $schedule->today_times_formatted }}</p>
        @endforeach
    </x-filament::card>
</x-filament::widget>

<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\ScheduleResource;
use App\Models\schedule;
use Saade\FilamentFullCalendar\Data\EventData;

class CalendarWidget extends FullCalendarWidget
{
   public function fetchEvents(array $fetchInfo): array
{
    return schedule::query()
        ->whereBetween('DATE', [$fetchInfo['start'], $fetchInfo['end']])
        ->with('employee')
        ->get()
        ->map(fn (schedule $schedule) => EventData::make()
            ->id($schedule->id)
            ->title($schedule->NAME)
            ->start("{$schedule->DATE}T{$schedule->STARTTIME}")
            ->end("{$schedule->DATE}T{$schedule->ENDTIME}")
            ->url(
                url: ScheduleResource::getUrl(name: 'view', parameters: ['record' => $schedule]),
                shouldOpenUrlInNewTab: true
            )
        )
        ->toArray();
}



    protected function getColorForScheduleType(string $type): string
    {
        return match ($type) {
            'WORKDAY' => '#34D399',
            'ONLEAVE' => '#60A5FA',
            'ABSENT'  => '#F87171',
            default   => '#D1D5DB',
        };
    }
}

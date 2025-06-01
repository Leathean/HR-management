<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\schedule;
use Filament\Facades\Filament;
class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


public function getTabs(): array
{
    $user = Filament::auth()->user();
    $employeeId = $user?->employee?->id;

    $baseQuery = Schedule::query()
        ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId));

    // Counts for LEAVESTATUS
    $allCount = (clone $baseQuery)->count();

    $workdayCount = (clone $baseQuery)->where('SCHEDULE_TYPE', 'WORKDAY')->count();
    $onleaveCount = (clone $baseQuery)->where('SCHEDULE_TYPE', 'ONLEAVE')->count();
    $restdayCount = (clone $baseQuery)->where('SCHEDULE_TYPE', 'RESTDAY')->count();
    $leavepayCount = (clone $baseQuery)->where('SCHEDULE_TYPE', 'LEAVEPAY')->count();

    return [
        // LEAVESTATUS Tabs
        Tab::make('All Schedules')
            ->badge($allCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),
        // ---

        // SCHEDULE_TYPE Tabs
        Tab::make('Workday')
            ->badge($workdayCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SCHEDULE_TYPE', 'WORKDAY')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('On Leave')
            ->badge($onleaveCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SCHEDULE_TYPE', 'ONLEAVE')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Restday')
            ->badge($restdayCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SCHEDULE_TYPE', 'RESTDAY')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', 'id', $employeeId))
            ),

        Tab::make('Leave with Pay')
            ->badge($leavepayCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SCHEDULE_TYPE', 'LEAVEPAY')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),
    ];
}
}

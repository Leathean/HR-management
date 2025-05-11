<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Attendance;
use Filament\Facades\Filament;
class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getTabs(): array
{
    $user = Filament::auth()->user();

    // Make sure the user is authenticated
    if (!$user) {
        return [];
    }

    $employeeId = $user->employee?->id;

    // Ensure the query is initialized properly
    $baseQuery = Attendance::query()
        ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId));

    // Count for each status
    $allCount = (clone $baseQuery)->count();
    $onTimeCount = (clone $baseQuery)->where('status', 'on time')->count();
    $lateCount = (clone $baseQuery)->where('status', 'late')->count();
    $absentCount = (clone $baseQuery)->where('status', 'absent')->count();

    // Return the tabs with the counts
    return [
        Tab::make('All')
            ->badge($allCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('On Time')
            ->badge($onTimeCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'on time')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Late')
            ->badge($lateCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'late')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Absent')
            ->badge($absentCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status', 'absent')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),
    ];
}


}

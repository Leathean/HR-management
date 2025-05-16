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

    if (!$user) {
        return [];
    }

    $employeeId = $user->employee?->id;

    $baseQuery = Attendance::query()
        ->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId));


    $allCount = (clone $baseQuery)->count();
    $presentCount = (clone $baseQuery)->where('status_day', 'PRESENT')->count();
    $absentCount = (clone $baseQuery)->where('status_day', 'ABSENT')->count();
    $lateCount = (clone $baseQuery)->where('time_in_status', 'LATE')->count();
    $earlyOutCount = (clone $baseQuery)->where('time_out_status', 'EARLY OUT')->count();
    return [
        Tab::make('All')
            ->badge((clone $baseQuery)->count())
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Present')
            ->badge((clone $baseQuery)->where('status_day', 'PRESENT')->count())
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status_day', 'PRESENT')
                    ->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Absent')
            ->badge((clone $baseQuery)->where('status_day', 'ABSENT')->count())
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('status_day', 'ABSENT')
                    ->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Late')
            ->badge((clone $baseQuery)->where('time_in_status', 'LATE')->count())
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('time_in_status', 'LATE')
                    ->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Early Out')
            ->badge((clone $baseQuery)->where('time_out_status', 'EARLY OUT')->count())
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('time_out_status', 'EARLY OUT')
                    ->when($user->ACCESS === 'EMPLOYEE', fn ($query) => $query->where('employees_id', $employeeId))
            ),
    ];
}


}

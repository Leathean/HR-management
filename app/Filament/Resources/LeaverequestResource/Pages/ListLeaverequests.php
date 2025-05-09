<?php
namespace App\Filament\Resources\LeaverequestResource\Pages;

use App\Filament\Resources\LeaverequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Leaverequest;
use Filament\Facades\Filament;

class ListLeaverequests extends ListRecords
{
    protected static string $resource = LeaverequestResource::class;
    protected static ?string $title = 'Leave Request Details';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create Leave Form'),
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-m-arrow-path')
                ->action(fn () => $this->resetTable()), // 🟢 Refresh table
        ];
    }

    public function resetTable(): void
    {

        $this->resetPage();
    }


    public function getTabs(): array
{
    $user = Filament::auth()->user();
    $employeeId = $user?->employee?->id;


    $baseQuery = Leaverequest::query()
        ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId));

    $allCount = (clone $baseQuery)->count();
    $pendingCount = (clone $baseQuery)->where('LEAVESTATUS', 'PENDING')->count();
    $acceptedCount = (clone $baseQuery)->where('LEAVESTATUS', 'ACCEPTED')->count();
    $deniedCount = (clone $baseQuery)->where('LEAVESTATUS', 'DENY')->count();

    return [
        Tab::make('All')
            ->badge($allCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Pending')
            ->badge($pendingCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('LEAVESTATUS', 'PENDING')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('Accepted')
            ->badge($acceptedCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('LEAVESTATUS', 'ACCEPTED')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),

        Tab::make('DENY')
            ->badge($deniedCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('LEAVESTATUS', 'DENY')
                    ->when($user && $user->ACCESS === 'EMPLOYEE', fn($query) => $query->where('employees_id', $employeeId))
            ),


    ];
}
}

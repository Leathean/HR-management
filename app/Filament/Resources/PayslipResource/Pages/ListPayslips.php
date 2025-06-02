<?php

namespace App\Filament\Resources\PayslipResource\Pages;

use App\Filament\Resources\PayslipResource;
use App\Models\Payslip;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Components\Tab;
use Filament\Facades\Filament;

class ListPayslips extends ListRecords
{
    protected static string $resource = PayslipResource::class;

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

        $baseQuery = Payslip::query()
            ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) =>
                $query->whereHas('payroll', fn ($q) => $q->where('employees_id', $employeeId))
            );

        $allCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('approval_status', 'PENDING')->count();
        $acceptedCount = (clone $baseQuery)->where('approval_status', 'ACCEPTED')->count();
        $deniedCount = (clone $baseQuery)->where('approval_status', 'DENIED')->count();

        return [
            Tab::make('All')
                ->badge($allCount)
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) =>
                        $query->whereHas('payroll', fn ($q) => $q->where('employees_id', $employeeId))
                    )
                ),

            Tab::make('Pending')
                ->badge($pendingCount)
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('approval_status', 'PENDING')
                        ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) =>
                            $query->whereHas('payroll', fn ($q) => $q->where('employees_id', $employeeId))
                        )
                ),

            Tab::make('Accepted')
                ->badge($acceptedCount)
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('approval_status', 'ACCEPTED')
                        ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) =>
                            $query->whereHas('payroll', fn ($q) => $q->where('employees_id', $employeeId))
                        )
                ),

            Tab::make('Denied')
                ->badge($deniedCount)
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('approval_status', 'DENIED')
                        ->when($user && $user->ACCESS === 'EMPLOYEE', fn ($query) =>
                            $query->whereHas('payroll', fn ($q) => $q->where('employees_id', $employeeId))
                        )
                ),
        ];
    }
}

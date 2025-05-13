<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payroll;
use Filament\Facades\Filament;
class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
{

    $baseQuery = Payroll::query();
    $allCount = (clone $baseQuery)->count();
    $pendingCount = (clone $baseQuery)->where('STATUS', 'PENDING')->count();
    $acceptedCount = (clone $baseQuery)->where('STATUS', 'PROCESSED')->count();


    return [
        Tab::make('All')
            ->badge($allCount),

        Tab::make('Pending')
            ->badge($pendingCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('STATUS', 'PENDING')
            ),

        Tab::make('Processed')
            ->badge($acceptedCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('STATUS', 'PROCESSED')

            ),
    ];
}
}

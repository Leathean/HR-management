<?php

namespace App\Filament\Resources\SalaryResource\Pages;

use App\Filament\Resources\SalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use App\Models\Salary;
class ListSalaries extends ListRecords
{
    protected static string $resource = SalaryResource::class;

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
    $baseQuery = Salary::query();

    $allCount = (clone $baseQuery)->count();
    $monthlyCount = (clone $baseQuery)->where('SALARY_TYPE', 'MONTHLY')->count();
    $dailyCount = (clone $baseQuery)->where('SALARY_TYPE', 'DAILY')->count();
    $biweeklyCount = (clone $baseQuery)->where('SALARY_TYPE', 'BIWEEKLY')->count();
    $enableCount = (clone $baseQuery)->where('STATUS', true)->count();
    $disabledCount = (clone $baseQuery)->where('STATUS', false)->count();

    return [
        Tab::make('All')
            ->badge($allCount),

        Tab::make('Monthly')
            ->badge($monthlyCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SALARY_TYPE', 'MONTHLY')
            ),

        Tab::make('Daily')
            ->badge($dailyCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SALARY_TYPE', 'DAILY')
            ),

        Tab::make('Biweekly')
            ->badge($biweeklyCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('SALARY_TYPE', 'BIWEEKLY')
            ),

        Tab::make('Active Salary')
            ->badge($enableCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('STATUS', true)
            ),


        Tab::make('Inactive Salary')
            ->badge($disabledCount)
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('STATUS', false)
            ),
    ];
}


}



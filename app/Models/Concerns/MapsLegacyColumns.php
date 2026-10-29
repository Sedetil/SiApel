<?php

namespace App\Models\Concerns;

use App\Models\MappedEloquentBuilder;

trait MapsLegacyColumns
{
    public function newEloquentBuilder($query): MappedEloquentBuilder
    {
        return new MappedEloquentBuilder($query);
    }

    public function qualifyColumn($column)
    {
        $column = $this->mapLegacyColumn($column);

        return parent::qualifyColumn($column);
    }

    public function mapLegacyColumn(string $column): string
    {
        $aliases = $this->legacyColumnAliases ?? [];

        if (str_contains($column, '.')) {
            [$table, $col] = explode('.', $column, 2);
            if (isset($aliases[$col])) {
                return $this->getTable().'.'.$aliases[$col];
            }

            return $column;
        }

        return $aliases[$column] ?? $column;
    }
}

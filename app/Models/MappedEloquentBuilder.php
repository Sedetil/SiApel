<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class MappedEloquentBuilder extends Builder
{
    public function mapCol($column)
    {
        if (is_string($column) && method_exists($this->model, 'mapLegacyColumn')) {
            return $this->model->mapLegacyColumn($column);
        }

        if (is_array($column) && method_exists($this->model, 'mapLegacyColumn')) {
            $mapped = [];
            foreach ($column as $k => $v) {
                $mapped[$this->model->mapLegacyColumn($k)] = $v;
            }

            return $mapped;
        }

        return $column;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_string($column) || is_array($column)) {
            $column = $this->mapCol($column);
        }

        return parent::where($column, $operator, $value, $boolean);
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if (is_string($column) || is_array($column)) {
            $column = $this->mapCol($column);
        }

        return parent::orWhere($column, $operator, $value);
    }

    public function whereDate($column, $operator = null, $value = null, $boolean = 'and')
    {
        return parent::whereDate($this->mapCol($column), $operator, $value, $boolean);
    }

    public function orWhereDate($column, $operator = null, $value = null)
    {
        return parent::orWhereDate($this->mapCol($column), $operator, $value);
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        return parent::whereIn($this->mapCol($column), $values, $boolean, $not);
    }

    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return parent::whereNotIn($this->mapCol($column), $values, $boolean);
    }

    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        if (is_string($columns)) {
            $columns = $this->mapCol($columns);
        } elseif (is_array($columns)) {
            $columns = array_map([$this, 'mapCol'], $columns);
        }

        return parent::whereNull($columns, $boolean, $not);
    }

    public function whereNotNull($columns, $boolean = 'and')
    {
        if (is_string($columns)) {
            $columns = $this->mapCol($columns);
        } elseif (is_array($columns)) {
            $columns = array_map([$this, 'mapCol'], $columns);
        }

        return parent::whereNotNull($columns, $boolean);
    }

    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        return parent::whereBetween($this->mapCol($column), $values, $boolean, $not);
    }

    public function orderBy($column, $direction = 'asc')
    {
        return parent::orderBy($this->mapCol($column), $direction);
    }

    public function latest($column = null)
    {
        if ($column) {
            $column = $this->mapCol($column);
        }

        return parent::latest($column);
    }

    public function oldest($column = null)
    {
        if ($column) {
            $column = $this->mapCol($column);
        }

        return parent::oldest($column);
    }

    public function pluck($column, $key = null)
    {
        $mappedCol = $this->mapCol($column);
        $mappedKey = $key ? $this->mapCol($key) : null;

        return parent::pluck($mappedCol, $mappedKey);
    }

    public function select($columns = ['*'])
    {
        if (is_array($columns)) {
            $mapped = array_map([$this, 'mapCol'], $columns);
        } else {
            $mapped = $this->mapCol($columns);
        }

        return parent::select($mapped);
    }

    public function addSelect($column)
    {
        if (is_array($column)) {
            $mapped = array_map([$this, 'mapCol'], $column);
        } else {
            $mapped = $this->mapCol($column);
        }

        return parent::addSelect($mapped);
    }

    public function value($column)
    {
        return parent::value($this->mapCol($column));
    }

    public function updateOrCreate(array $attributes, \Closure|array $values = [])
    {
        $mappedAttrs = $this->mapCol($attributes);

        return parent::updateOrCreate($mappedAttrs, $values);
    }

    public function update(array $values)
    {
        $mapped = [];
        foreach ($values as $k => $v) {
            $mapped[$this->mapCol($k)] = $v;
        }

        return parent::update($mapped);
    }

    public function insert(array $values)
    {
        if (empty($values)) {
            return parent::insert($values);
        }

        if (is_array(reset($values))) {
            $mapped = array_map([$this, 'mapCol'], $values);
        } else {
            $mapped = $this->mapCol($values);
        }

        return parent::insert($mapped);
    }

    public function firstOrCreate(array $attributes = [], \Closure|array $values = [])
    {
        $mappedAttrs = $this->mapCol($attributes);

        return parent::firstOrCreate($mappedAttrs, $values);
    }
}

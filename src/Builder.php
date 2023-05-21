<?php

namespace Sofa\Hookable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method $this leftJoin($table, $one, $operator, $two)
 */
class Builder extends EloquentBuilder {
    
    /*
    |--------------------------------------------------------------------------
    | Hooks handling
    |--------------------------------------------------------------------------
    */
    
    public function select($columns = ['*']) {
        return $this->callHookOrParent(__FUNCTION__, $columns);
    }
    
    /**
     * Call custom handlers for where call.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    protected function callHookOrParent($method, ...$arguments) {
        $model = $this->getModel();
        if ($model->hookExists($method))
            return $model->callHook($method, $arguments);
        
        return $this->callParent($method, $arguments);
    }
    
    /**
     * @return Model|Hookable|Builder
     */
    public function getModel() {
        return $this->model;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Query builder overrides
    |--------------------------------------------------------------------------
    */
    
    /**
     * Call base Eloquent method.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function callParent($method, array $args) {
        return call_user_func_array(EloquentBuilder::class . "::{$method}", $args);
    }
    
    public function where($column, $operator = null, $value = null, $boolean = 'and') {
        // If developer provided column prefixed with table name we will
        // not even try to map the column, since obviously the value
        // refers to the actual column name on the queried table.
        if (str_contains($column, '.'))
            return $this->callParent(__FUNCTION__, [$column, $operator, $value, $boolean]);
        
        return $this->callHookOrParent(__FUNCTION__, $column, $operator, $value, $boolean);
    }
    
    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false) {
        return $this->callHookOrParent(__FUNCTION__, $column, $values, $boolean, $not);
    }
    
    public function whereIn($column, $values, $boolean = 'and', $not = false) {
        return $this->callHookOrParent(__FUNCTION__, $column, $values, $boolean, $not);
    }
    
    public function whereNull($columns, $boolean = 'and', $not = false) {
        return $this->callHookOrParent(__FUNCTION__, $columns, $boolean, $not);
    }
    
    public function orderBy($column, $direction = 'asc') {
        return $this->callHookOrParent(__FUNCTION__, $column, $direction);
    }
    
    public function pluck($column, $key = null) {
        return $this->callHookOrParent(__FUNCTION__, $column, $key);
    }
    
    public function value($column) {
        return $this->callHookOrParent(__FUNCTION__, $column);
    }
    
    public function aggregate($function, $columns = ['*']) {
        return $this->callHookOrParent(__FUNCTION__, $function, $columns);
    }
    
    /**
     * Get a new instance of the Hookable query builder.
     *
     * @return Builder
     */
    public function newQuery() {
        return $this->model->newQueryWithoutScopes();
    }
    
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and') {
        return $this->callHookOrParent("where{$type}",
            $column,
            $operator,
            $value,
            $boolean);
    }
}

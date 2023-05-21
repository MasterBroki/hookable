<?php

namespace Sofa\Hookable;

use Closure;

/**
 * This trait is an entry point for all the hooks that we want to apply
 * on the Eloquent Model and Builder in order to let the magic happen.
 *
 * @version 2.0
 */
trait Hookable {
    
    /*
    |--------------------------------------------------------------------------
    | Hooks handling
    |--------------------------------------------------------------------------
    */
    
    /**
     * Register hook on Eloquent method.
     *
     * @param string $method
     * @param Closure $hook
     * @return void
     */
    public static function hook($method, Closure $hook) {
        HookableStorage::setHook(get_class(), $method, $hook);
    }
    
    /**
     * Create new Hookable query builder for the instance.
     *
     * @param \Illuminate\Database\Query\Builder
     * @return Builder
     */
    public function newEloquentBuilder($query) {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $query);
        return new Builder($query);
    }
    
    public static function getHooks() {
        $class = get_class();
        $hooks = HookableStorage::getAllHooks();
        return $hooks[$class] ?? [];
    }
    
    public function callHook($method, ...$arguments) {
        $hooks = HookableStorage::getAllHooks()[get_class()];
        if ($method == "where" && count(explode('.', $arguments[1])) > 1) {
            return $this->model->$method(... $arguments);
        }
        
        $callBack = $hooks[$method];
        $this->onHookCalled($method, $arguments);
        return call_user_func_array($callBack, $arguments);
    }
    
    public function hookExists($method) {
        return HookableStorage::hasHooks(get_class(), $method);
    }
    
    protected function onHookCalled($name, $arguments) {
    
    }
    
    /**
     * Remove all of the hooks on the Eloquent model.
     *
     * @return void
     */
    public static function flushHooks() {
        HookableStorage::flushHooksForClass(get_class());
    }
    
    /*
    |--------------------------------------------------------------------------
    | Hook decorators
    |--------------------------------------------------------------------------
    */
    public function getAttribute($key) {
        $attributeName = "get" . $this->camelize($key) . "Attribute";
        if ($this->hookExists($attributeName))
            return $this->callHook($attributeName);
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $key);
        return parent::getAttribute($key);
    }
    
    private function camelize($input, $separator = '_') {
        return str_replace($separator, '', ucwords($input, $separator));
    }
    
    public function setAttribute($key, $value) {
        if ($this->hookExists("set" . $key . "Attribute"))
            return $this->callHook("set" . $key . "Attribute", $value);
        return parent::setAttribute($key, $value);
    }
    
    public function save(array $options = []) {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $options);
        return parent::save($options);
    }
    
    public function isDirty($attributes = null) {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $attributes);
        return parent::isDirty($attributes);
    }
    
    public function toArray() {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__);
        return parent::toArray();
    }
    
    public function replicate(array $except = null) {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $except);
        return parent::replicate($except);
    }
    
    public function __isset($key) {
        if ($this->hookExists(__FUNCTION__))
            return $this->callHook(__FUNCTION__, $key);
        return parent::__isset($key);
    }
    
    public function __unset($key) {
        if ($this->hookExists(__FUNCTION__))
            $this->callHook(__FUNCTION__, $key);
        parent::__unset($key);
    }
}

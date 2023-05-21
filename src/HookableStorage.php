<?php

namespace Sofa\Hookable;

class HookableStorage {
    /**
     * @var string[Closure[]]
     */
    private static $hooks = [];
    
    /**
     * Remove all hooks on the specified class.
     *
     * @return void
     */
    public static function flushHooksForClass($class) {
        self::$hooks[$class] = [];
    }
    
    /**
     * Remove all hooks.
     *
     * @return void
     */
    public static function flushHooks() {
        self::$hooks = [];
    }
    
    public static function getAllHooks() {
        return self::$hooks;
    }
    
    public static function setHook($class, $method, $callback) {
        if (!isset(self::$hooks[$class]))
            self::$hooks[$class] = [];
        self::$hooks[$class][$method] = $callback;
    }
    
    public static function hasHooks($class, $method) {
        return isset(self::$hooks[$class][$method]) ?? false;
    }
}

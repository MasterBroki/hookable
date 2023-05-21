<?php


namespace HookableStorageTest;

use PHPUnit\Framework\TestCase;
use Sofa\Hookable\Hookable;
use Sofa\Hookable\HookableStorage;

class HookableStorageTest extends TestCase {
    
    public function testFlushesAllHooks() {
        HookableStorage::flushHooks();
        HookableDummy::hook('method1', function ($next, $value, $args) {
        });
        HookableDummy::hook('method2', function ($next, $value, $args) {
        });
        $hooks = HookableDummy::getHooks();
        self::assertCount(2, $hooks);
        
        HookableStorage::flushHooks();
        
        self::assertCount(0, HookableDummy::getHooks());
        
    }
}

class ModelDummy extends \Illuminate\Database\Eloquent\Model {

}

class HookableDummy extends ModelDummy {
    use Hookable;
}

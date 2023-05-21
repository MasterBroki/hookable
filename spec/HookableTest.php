<?php


use PHPUnit\Framework\TestCase;
use Sofa\Hookable\Hookable;
use Sofa\Hookable\HookableStorage;

class HookableTest extends TestCase {
    public function testGetAttributeHook() {
        HookableStorage::flushHooks();
        HookableDummy::hook('getAttribute', function ($key) {
            return 'bar';
        });
        
        self::assertCount(1, HookableStorage::getAllHooks());
        self::assertCount(1, HookableDummy::getHooks());
        
        $dummy = new HookableDummy();
        self::assertEquals('bar', $dummy->getAttribute('attribute'));
    }
    
    public function testHookOnAttribute() {
        HookableStorage::flushHooks();
        HookableDummy::hook('getAttributeAttribute', function () {
            return 'bar';
        });
        
        self::assertCount(1, HookableStorage::getAllHooks());
        self::assertCount(1, HookableDummy::getHooks());
        
        $dummy = new HookableDummy();
        self::assertEquals('bar', $dummy->getAttribute('attribute'));
    }
    
    
    public function testDifferentHooksOnDifferentClasses() {
        HookableStorage::flushHooks();
        HookableDummy::hook('method1', function ($next, $value, $args) {
        });
        HookableDummy::hook('method2', function ($next, $value, $args) {
        });
        HookableDumbDumb::hook('method3', function ($next, $value, $args) {
        });
    
        self::assertCount(2, HookableDummy::getHooks());
        self::assertCount(1, HookableDumbDumb::getHooks());
    
        HookableDummy::flushHooks();
    
        self::assertCount(0, HookableDummy::getHooks());
        self::assertCount(1, HookableDumbDumb::getHooks());
    }
}

class ModelDummy {
    protected function getAttribute($key) {
        return 'foo';
    }
}


class HookableDummy extends ModelDummy {
    use Hookable;
}


class HookableDumbDumb extends ModelDummy {
    use Hookable;
}

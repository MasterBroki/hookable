<?php


use Sofa\Hookable\Hookable;
use PHPUnit\Framework\TestCase;

class HookableTest extends TestCase {
    public function testResolvesHooksInInstanceScope(){
    
        HookableDummy::hook('getAttribute', function ($next, $value, $args) {
            return 'bar';
        });
        
        self::assertCount(1, HookableDummy::getHooks());
    
        $dummy = new HookableDummy();
        self::assertEquals('bar', $dummy->getAttribute('attribute'));
    }
    public function testFlushesAllHooks(){
    
        HookableDummy::hook('method1', function ($next, $value, $args) {});
        HookableDummy::hook('method2', function ($next, $value, $args) {});
    
        self::assertCount(2, HookableDummy::getHooks());
    
        HookableDummy::flushHooks();
    
        self::assertCount(0, HookableDummy::getHooks());
    
    }
    public function testDifferentHooksOnDifferentClasses(){
    
        HookableDummy::hook('method1', function ($next, $value, $args) {});
        HookableDummy::hook('method2', function ($next, $value, $args) {});
        HookableDumbDumb::hook('method3', function ($next, $value, $args) {});
    
        self::assertCount(2, HookableDummy::getHooks());
        self::assertCount(1, HookableDumbDumb::getHooks());
    
        HookableDummy::flushHooks();
    
        self::assertCount(0, HookableDummy::getHooks());
        self::assertCount(1, HookableDumbDumb::getHooks());
    
    }
}

class ModelDummy{
    public function getAttribute($key) {
        return 'foo';
    }
}


class HookableDummy extends ModelDummy{
    use Hookable;
}


class HookableDumbDumb extends ModelDummy{
    use Hookable;
}

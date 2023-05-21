<?php


use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Sofa\Hookable\Builder;

class BuilderTest extends TestCase {
    
    public function testFallbackToBaseColumnsForPrefixedColumns() {
        $dumbModel = $this->getModel();
        $dumbModel->where('prefixed.column', 'value');
        self::assertEquals(1, $this->sofaBuilder->callParentCount);
    }
    
    public function testCallsHookDefinedOnModel() {
        $dumbModel = $this->getModel();
        DumbModel::hook('select', function ($columns) {
            return 'test';
        });
        $dumbModel->select(['column', 'value']);
        self::assertEquals(1, $dumbModel->queryHookCount, implode(', ', $dumbModel->calledHooks));
        
    }
    
    public function getModel($driver = 'MySql') {
        $model = new DumbModel();
        $grammarClass = "Illuminate\Database\Query\Grammars\\{$driver}Grammar";
        $processorClass = "Illuminate\Database\Query\Processors\\{$driver}Processor";
        $grammar = new $grammarClass;
        $processor = new $processorClass;
        
        $schema = Mockery::mock('StdClass');
        $schema->shouldReceive('getColumnListing')->andReturn(['id', 'first_name', 'last_name']);
        
        $connection = Mockery::mock(Connection::class)->makePartial();
        $connection->shouldReceive('getQueryGrammar')->andReturn($grammar);
        $connection->shouldReceive('getPostProcessor')->andReturn($processor);
        $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
        $connection->shouldReceive('getName')->andReturn($driver);
        
        $query = new Illuminate\Database\Query\Builder($connection, $grammar, $processor);
        $this->sofaBuilder = new DumbSofaBuilder($query);
        DumbModel::hook('newEloquentBuilder', function () {
            return $this->sofaBuilder;
        });
        
        $resolver = Mockery::mock(ConnectionResolver::class)->makePartial();
        $resolver->shouldReceive('connection')->andReturn($connection);
        /** @var Model|string $class */
        DumbModel::setConnectionResolver($resolver);
        return $model;
    }
}

class DumbSofaBuilder extends Builder {
    public $callParentCount = 0;
    
    public function callParent($method, array $args) {
        $this->callParentCount++;
        return parent::callParent($method, $args);
    }
}

class DumbModel extends \Illuminate\Database\Eloquent\Model {
    use \Sofa\Hookable\Hookable;
    
    public $queryHookCount = 0;
    public $calledHooks = [];
    
    public function onHookCalled($name, $arguments) {
        if ($name == "newEloquentBuilder")
            return;
        $this->queryHookCount++;
        $this->calledHooks[] = $name;
    }
}

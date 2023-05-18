<?php


use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Sofa\Hookable\ArgumentBag;
use Sofa\Hookable\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase {
    protected function setUp(): void {
        $grammar = new Illuminate\Database\Query\Grammars\MySqlGrammar();
        $postProcessor = new MySqlProcessor();
        $query = new Illuminate\Database\Query\Builder($this->getMockBuilder(ConnectionInterface::class)
            ->setMockClassName('DumbConnection')
            ->getMock(), $grammar, $postProcessor);
        $this->sofaBuilder = new DumbSofaBuilder($query);
    }
    
    public function testFallbackToBaseColumnsForPrefixedColumns() {
        self::assertEquals('query', $this->sofaBuilder->where('prefixed.column', 'value'));
    }
    
    public function testCallsHookDefinedOnModel() {
        $dumbModel = new DumbModel();
        $this->sofaBuilder->setMockedModel($dumbModel);
        $this->sofaBuilder->select(['column', 'value']);
        self::assertEquals(1, $dumbModel->queryHookCount);
        
    }
}

class DumbSofaBuilder extends Builder {
    public function __construct(\Illuminate\Database\Query\Builder $query) {
        parent::__construct($query);
    }
    
    public function callParent($method, array $args) {
        return 'query';
    }
    
    public function setMockedModel(DumbModel $model){
        $this->dumbModel = $model;
    }
    
    public function getModel() {
        return $this->dumbModel;
    }
}

class DumbModel{
    public $queryHookCount = 0;
    
    public function queryHook(){
        $this->queryHookCount++;
    }
}

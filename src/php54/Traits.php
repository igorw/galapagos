<?php

namespace galapagos\php54;

class Traits extends \PHPParser_NodeVisitorAbstract {
    public function __construct(Traits_Collector $traitsCollector = null) {
        $this->traitsCollector = $traitsCollector ?: new Traits_Collector;
    }
    
    public function beforeTraverse(array $nodes) {
        $traverser = new \PHPParser_NodeTraverser;
        $traverser->addVisitor($this->traitsCollector);
        $traverser->traverse($nodes);
    }

    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            foreach ($node->stmts as $statement) {
                if ($statement instanceof \PHPParser_Node_Stmt_TraitUse) {
                    $stmts = [$node->stmts];
                    foreach ($statement->traits as $nameNode) {
                        if ($this->traitsCollector->hasTrait((string) $nameNode)) {
                            $stmts[] = $this->traitsCollector->getTrait((string) $nameNode)->stmts;
                        }
                    }
                    $node->stmts = call_user_func_array('array_merge', $stmts);
                }
            }
            return $node;
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Trait) {
            return false;
        }
        
        if ($node instanceof \PHPParser_Node_Stmt_TraitUse) {
            return false;
        }
    }
}

class Traits_Collector extends \PHPParser_NodeVisitorAbstract {
    protected $traits = array();
    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Trait) {
            $this->traits[$node->name] = $node;
        }
    }
    public function hasTrait($name) {
        return isset($this->traits[$name]);
    }
    public function getTrait($name) {
        return $this->traits[$name];
    }
}
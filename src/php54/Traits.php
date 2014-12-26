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
                    foreach ($statement->traits as $traitName) {
                        if ($this->traitsCollector->hasTrait((string) $traitName)) {
                            $trait = $this->traitsCollector->getTrait((string) $traitName);
                            $traverser = new \PHPParser_NodeTraverser();
                            $traverser->addVisitor(
                                new Traits_ConflictResolver(
                                    $node,
                                    $trait,
                                    $statement
                                )
                            );
                            $stmts[] = $traverser->traverse($trait->stmts);
                        } else {
                            throw new \Exception(
                                sprintf('Could not find referenced trait "%s"', $traitName)
                            );
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
    public static $traits = array();

    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Trait) {
            self::$traits[$node->name] = $node;
        }
    }

    public function hasTrait($name) {
        return isset(self::$traits[$name]);
    }

    public function getTrait($name) {
        return self::$traits[$name];
    }
}

class Traits_ConflictResolver extends \PHPParser_NodeVisitorAbstract {
    protected $classMethods = [];
    protected $trait;
    protected $traitUse;

    public function __construct(
        \PHPParser_Node_Stmt_Class $class,
        \PHPParser_Node_Stmt_Trait $trait,
        \PHPParser_Node_Stmt_TraitUse $traitUse
    ) {
        foreach ($class->getMethods() as $method) {
            $this->classMethods[] = $method->name;
        }
        $this->trait = $trait;
        $this->traitUse = $traitUse;
    }

    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            foreach ($this->traitUse->adaptations as $adaption) {
                if ($adaption instanceof \PHPParser_Node_Stmt_TraitUseAdaptation_Alias
                    && (string) $adaption->trait === $this->trait->name
                    && $adaption->method === $node->name) {
                    $node->name = $adaption->newName;
                    return $node;
                }
            }
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod
            && in_array($node->name, $this->classMethods)) {
            return false;
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            foreach ($this->traitUse->adaptations as $adaption) {
                if ($adaption instanceof \PHPParser_Node_Stmt_TraitUseAdaptation_Precedence) {
                    if ($adaption->method === $node->name) {
                        foreach ($adaption->insteadof as $insteadof) {
                            if ((string) $insteadof === $this->trait->name) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
    }

}

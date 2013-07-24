<?php

namespace galapagos\php54;

function transform_code($code, callable $transform) {
    $parser = new \PHPParser_Parser(new \PHPParser_Lexer);
    $prettyPrinter = new \PHPParser_PrettyPrinter_Default;

    $ast = $parser->parse($code);
    $ast = $transform($ast);
    return '<?php'."\n\n".$prettyPrinter->prettyPrint($ast);
}

function transform_with_visitors($code, array $visitors) {
    return transform_code($code, function ($ast) use ($code, $visitors) {
        $traverser = new \PHPParser_NodeTraverser;
        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }
        return $traverser->traverse($ast);
    });
}

function transform_short_array($code) {
    return transform_with_visitors($code, []);
}

function transform_closure_this($code) {
    return transform_with_visitors($code, [
        new NodeVisitor_ClosureThis,
    ]);
}

function transform_array_deref($code) {
    return transform_with_visitors($code, [
        new NodeVisitor_ArrayDeref,
    ]);
}

function transform_callable_typehint($code) {
    return transform_with_visitors($code, [
        new NodeVisitor_CallableTypehint,
    ]);
}

class NodeVisitor_ClosureThis extends \PHPParser_NodeVisitorAbstract {
    private $method = null;
    private $closure = null;

    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->method = $node;
        }

        if ($this->method && $node instanceof \PHPParser_Node_Expr_Closure) {
            $this->closure = $node;
        }

        if ($this->closure
            && $node instanceof \PHPParser_Node_Expr_Variable
            && 'this' === $node->name) {

            $this->method->setAttribute('has_that', true);
            $this->closure->setAttribute('has_that', true);
            return new \PHPParser_Node_Expr_Variable('that');
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Expr_Closure) {
            if ($this->closure->getAttribute('has_that')) {
                $node->uses = array_merge($node->uses, [
                    new \PHPParser_Node_Expr_Variable('that'),
                ]);
            }

            $this->closure = null;
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            if ($this->method->getAttribute('has_that')) {
                $node->stmts = array_merge([
                    new \PHPParser_Node_Expr_Assign(
                        new \PHPParser_Node_Expr_Variable('that'),
                        new \PHPParser_Node_Expr_Variable('this')
                    ),
                ], $node->stmts);
            }

            $this->method = null;
            $this->closure = null;
        }
    }
}

class NodeVisitor_ArrayDeref extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Expr_ArrayDimFetch
            && $node->var instanceof \PHPParser_Node_Expr_FuncCall) {

            $tmp = new \PHPParser_Node_Expr_Variable('tmp');
            $null = new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('null'));

            return new \PHPParser_Node_Expr_Ternary(
                new \PHPParser_Node_Expr_Assign($tmp, $node->var),
                new \PHPParser_Node_Expr_ArrayDimFetch($tmp, $node->dim),
                $null
            );
        }
    }
}

class NodeVisitor_CallableTypehint extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_Function) {
            $callableParams = [];
            foreach ($node->params as $i => $param) {
                if ('callable' === $param->type) {
                    $param->type = null;
                    $callableParams[] = [
                        'name'      => $param->name,
                        'position'  => $i + 1,
                    ];
                }
            }

            $typeChecks = array_map([$this, 'createTypeCheckNode'], $callableParams);
            $node->stmts = array_merge($typeChecks, $node->stmts);
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
    }

    // if (!is_callable($bar)) {
    //     trigger_error(sprintf('Argument 1 passed to %s() must be callable, %s given', __FUNCTION__, gettype($bar)), E_ERROR);
    // }
    public function createTypeCheckNode($callableParam) {
        return new \PHPParser_Node_Stmt_If(
            new \PHPParser_Node_Expr_BooleanNot(
                new \PHPParser_Node_Expr_FuncCall(
                    new \PHPParser_Node_Name('is_callable'),
                    [
                        new \PHPParser_Node_Expr_Variable($callableParam['name']),
                    ]
                )
            ),
            [
                'stmts' => [
                    new \PHPParser_Node_Expr_FuncCall(
                        new \PHPParser_Node_Name('trigger_error'),
                        [
                            new \PHPParser_Node_Expr_FuncCall(
                                new \PHPParser_Node_Name('sprintf'),
                                [
                                    new \PHPParser_Node_Scalar_String(
                                        "Argument {$callableParam['position']} passed to %s() must be callable, %s given"
                                    ),
                                    new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('__FUNCTION__')),
                                    new \PHPParser_Node_Expr_FuncCall(
                                        new \PHPParser_Node_Name('gettype'),
                                        [
                                            new \PHPParser_Node_Expr_Variable($callableParam['name']),
                                        ]
                                    ),
                                ]
                            ),
                            new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('E_ERROR')),
                        ]
                    ),
                ],
            ]
        );
    }
}

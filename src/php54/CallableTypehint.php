<?php

namespace galapagos\php54;

class CallableTypehint extends \PHPParser_NodeVisitorAbstract {
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

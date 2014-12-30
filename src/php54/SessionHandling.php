<?php

namespace galapagos\php54;

class SessionHandling extends \PHPParser_NodeVisitorAbstract {

    public function enterNode(\PHPParser_Node $node) {

        if ($node instanceof \PhpParser\Node\Expr\FuncCall &&
            $node->name == "session_set_save_handler" &&
            count($node->args) <= 2)
        {
            $handlers = ['open', 'close', 'read', 'write', 'destroy', 'gc'];
            $args = [];

            foreach($handlers as $handler) {
                array_push($args, new \PhpParser\Node\Arg(new \PhpParser\Node\Expr\Array_([
                    $node->args[0]->value,
                    new \PhpParser\Node\Scalar\String($handler)
                ])));
            }

            $node->args = $args;

            // register shutdown?
            if (isset($node->args[1]) && $node->args[1] == true) {
                // $left = clone $node;
                // $right = new \PhpParser\Node\Expr\FuncCall('register_shutdown_function', array(new \PhpParser\Node\Scalar\String('session_write_close')));
                // $node = new \PhpParser\Node\Expr\BinaryOp\BooleanAnd($left, $right);
            }

            return $node;
        }
    }

}



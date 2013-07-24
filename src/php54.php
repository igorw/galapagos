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
        new ClosureThis,
    ]);
}

function transform_array_deref($code) {
    return transform_with_visitors($code, [
        new ArrayDeref,
    ]);
}

function transform_callable_typehint($code) {
    return transform_with_visitors($code, [
        new CallableTypehint,
    ]);
}

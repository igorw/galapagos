<?php

namespace galapagos;

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

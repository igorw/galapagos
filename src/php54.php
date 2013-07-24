<?php

namespace galapagos\php54;

use galapagos as g;

function transform_short_array($code) {
    return g\transform_with_visitors($code, []);
}

function transform_closure_this($code) {
    return g\transform_with_visitors($code, [
        new ClosureThis,
    ]);
}

function transform_array_deref($code) {
    return g\transform_with_visitors($code, [
        new ArrayDeref,
    ]);
}

function transform_callable_typehint($code) {
    return g\transform_with_visitors($code, [
        new CallableTypehint,
    ]);
}

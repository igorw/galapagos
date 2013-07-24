<?php

namespace galapagos\php54;

use galapagos\abstract_test_case;

class callable_typehint_test extends abstract_test_case {
    /** @dataProvider provide_tests */
    public function test_transform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_callable_typehint($code)),
            $name
        );
    }

    public function provide_tests() {
        return $this->getTests(__DIR__.'/callable_typehint', 'test');
    }
}

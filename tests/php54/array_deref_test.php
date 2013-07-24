<?php

namespace galapagos\php54;

use galapagos\abstract_test_case;

class array_deref_test extends abstract_test_case {
    /** @dataProvider provide_tests */
    public function test_transform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_array_deref($code)),
            $name
        );
    }

    public function provide_tests() {
        return $this->getTests(__DIR__.'/array_deref', 'test');
    }
}

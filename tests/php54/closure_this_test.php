<?php

namespace galapagos\php54;

use galapagos\abstract_test_case;

class closure_this_test extends abstract_test_case {
    /** @dataProvider provide_tests */
    public function test_transform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_closure_this($code)),
            $name
        );
    }

    public function provide_tests() {
        return $this->getTests(__DIR__.'/closure_this', 'test');
    }
}

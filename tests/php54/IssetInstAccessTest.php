<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class IssetInstAccessTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_isset_inst_access($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/isset_inst_access', 'test');
    }
}



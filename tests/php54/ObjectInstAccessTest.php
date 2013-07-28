<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class ObjectInstAccessTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_object_inst_access($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/object_inst_access', 'test');
    }
}

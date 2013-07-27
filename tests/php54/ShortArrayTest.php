<?php

namespace galapagos\php54;

const FILE_HEAD = "<?php\n\n";

class ShortArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideTests */
    function testTransform($expected, $code) {
        $this->assertSame(FILE_HEAD.$expected, transform_short_array(FILE_HEAD.$code));
    }

    function provideTests() {
        return [
            'short_array_empty'     => [
                'return array();',
                'return [];',
            ],
            'short_array_list'      => [
                'return array(1, 2, 3);',
                'return [1, 2, 3];',
            ],
            'short_array_map'       => [
                "return array('foo' => 'bar', 'baz' => 'qux');",
                "return ['foo' => 'bar', 'baz' => 'qux'];",
            ],
            'short_array_nested'    => [
                "return array('foo' => array('+', 1, 2, array('+', 0, 3)));",
                "return ['foo' => ['+', 1, 2, ['+', 0, 3]]];",
            ],
        ];
    }
}

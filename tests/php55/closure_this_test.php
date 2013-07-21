<?php

namespace galapagos\php55;

class closure_this_test extends \PHPUnit_Framework_TestCase
{
    function test_closure_this() {
        $this->assertSame(<<<'EOF'
<?php class foo
{
    public function bar()
    {
        $that = $this;
        return function () use($that) {
            return $that->baz();
        };
    }
    public function baz()
    {
        return 'foo:bar:baz';
    }
}
EOF
            ,
            transform_closure_this(<<<'EOF'
<?php class foo
{
    public function bar()
    {
        return function () {
            return $this->baz();
        };
    }
    public function baz()
    {
        return 'foo:bar:baz';
    }
}
EOF
            )
        );
    }
}

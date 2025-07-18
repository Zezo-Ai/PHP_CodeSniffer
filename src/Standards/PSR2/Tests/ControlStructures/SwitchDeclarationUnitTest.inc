<?php
switch ($expr) {
    case 0:
        echo 'First case, with a break';
        break;
    case 1:
        echo 'Second case, which falls through';
        // no break
    case 2:
    case   3:
    Case 4:
        echo 'Third case, return instead of break';
        return;
    Default:
        echo 'Default case';
            break;
}

switch ($expr) {
    case 0:
        echo 'First case,';

    case 1 :
        echo 'Second case';
        // no break
    case 2:
    case 3:
        echo 'Third case';
    return;

    default:
        echo 'Default case';
    break;
}

switch ($foo) {
    case'Foo': {
        echo 'foo';
        break;
        }
}

while ($i < 10) {
    switch ($foo) {
        case '1':
        case '2':
            ++$i;
            continue 2;
        case '3':
            return $i;
    }
}

switch (true) {
    case is_resource($value):
        throw new Exception('foo');
    case is_object($value):
        return 'object';
}

switch (0) {
    case 0:
        switch (1) {
            case 1:
                echo 'a';
                break;
        }
        break;
}

switch ($foo) {
    case Foo::ONE:
    case Foo::TWO:
    case Foo::Class:
        break;
}

switch (true) {
    case $value instanceof StdClass:
        return 1;
    case strpos('_', get_class($value)) !== false:
        break;
}

switch (true) {
    case $value instanceof StdClass:
        if ($value) {
            return null;
        }
}

use Vendor\Test\FooBar;

function test()
{
    switch ($val) {
        case 'foo':
            echo 'foo';
            break;
        default:
            echo 'foo';
    }

    exit;
}

switch ($foo) {
    case 1: $bar = 1; break;
    case 2:
    
        $bar = 2; break;
    case 21:
    case 3: return 3;
    default: $bar = 0;
}

switch ($foo) {
    case 'foo': // some comment
        echo 'foo';
        break;
    case 'bar':
        // some comment
        echo 'bar';
        break;
    case 'baz': // phpcs:ignore Standard.Category.Sniff
        echo 'baz';
        break;
    case 'boo':

        // other comment
        echo 'boo';
        break;
    default: // other comment
        echo 'default';
        break;
}

switch($foo)
{
    case ('foo'):
    default:
    {
        $foo = 'foo';
        break;
    }
}

switch ($foo) {
    case Foo::INTERFACE:
        return self::INTERFACE;
    case Foo::NAMESPACE:
        return self::MODULE;
    case Foo::TRAIT:
    case Foo::ARRAY:
        return self::VALUE;
}

// OK: Every clause terminates
switch ($foo) {
    case 1:
        if ($bar > 0) {
            return 0;
        } else {
            return 1;
        }
    case 2:
        return 2;
}

// ERROR: No else clause
switch ($foo) {
    case 1:
        if ($bar > 0) {
            return 0;
        } elseif ($bar < 0) {
            return 1;
        }
    case 2:
        return 2;
}

// OK: No fall-through present
switch ($foo) {
    case 1:
        if ($bar > 0) {
            return 0;
        } elseif ($bar < 0) {
            return 1;
        }
}

// ERROR: No else clause (nested)
switch ($foo) {
    case 1:
        if ($bar > 0) {
            return 0;
        } else {
            if ($foo > $bar) {
                continue;
            }
        }
    case 2:
        return 2;
}

// OK: Every clause terminates
switch ($foo) {
    case 1:
        if ($bar > 0) {
            return 0;
        } else {
            if ($foo > $bar) {
                continue;
            } else {
                break;
            }
        }
    case 2:
        return 2;
}

// ERROR: Non-termination IF clause
switch ($foo) {
    case 1:
        if ($bar > 0) {
            $offset = 0;
        } else {
            break;
        }
    case 2:
        return 2;
}

// ERROR: Non-termination IF clause (nested)
switch ($foo) {
    case 1:
        if ($bar > 0) {
            continue;
        } else {
            if ($foo > $bar) {
                $offset = 0;
            } else {
                break;
            }
        }
    case 2:
        return 2;
}

switch ($sContext)
{
    case 'SOMETHING':
    case 'CONSTANT':
        do_something();
        break;
    case 'GLOBAL':
    case 'GLOBAL1':
        do_something();
        // Fall through
    default:
    {
        do_something();
    }
}

$foo = $foo ?
    function () {
        switch ($a) {
            case 'a':
                break;
        }
    } :
    null;

switch ($foo) {
case Foo::INTERFACE:
    echo '1';
    return self::INTERFACE;
case Foo::TRAIT:
case Foo::ARRAY:
    echo '1';
    return self::VALUE;
}

// OK: Every clause terminates
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                return 1;
            default:
                return 3;
        }
    case 2:
        return 2;
}

// KO: Not every clause terminates
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                return;
        }
    case 2:
        return 2;
}

// KO: Not every clause terminates
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                return;
            default:
                $a = 1;
        }
    case 2:
        return 2;
}

// OK: Every clause terminates
switch ($foo) {
    case 1:
        switch ($bar) {
            case 1:
                return 1;
            default:
                throw new \Exception();
        }
    case 2:
        return 2;
}

switch ($foo) {
    case 1:
        // phpcs:ignore
    case 2:
        return 1;
    case 3:
        return 2;
}

// Issue 3352.
switch ( $test ) {
    case 2: // comment followed by empty line

        break;

    case 3: /* phpcs:ignore Stnd.Cat.SniffName -- Verify correct handling of ignore comments. */



        break;

    case 4: /** inline docblock */



        break;

    case 5: /* checking how it handles */ /* two trailing comments */

        break;

    case 6:
       // Comment as first content of the body.

        break;

    case 7:
        /* phpcs:ignore Stnd.Cat.SniffName -- Verify correct handling of ignore comments at start of body. */

        break;

    case 8:
        /** inline docblock */

        break;
}

// Handle comments correctly.
switch ($foo) {
    case 1:
        if ($bar > 0) {
            doSomething();
        }
        // Comment
        else {
            return 1;
        }
    case 2:
        return 2;
}

switch ($foo) {
    case 1:
        if ($bar > 0) /*comment*/ {
            return doSomething();
        }
        else {
            return 1;
        }
    case 2:
        return 2;
}

// Issue #3297.
// Okay - finally will always be executed, so all branches are covered by the `return` in finally.
switch ( $a ) {
    case 1:
        try {
            doSomething();
        } catch (Exception $e) {
            doSomething();
        } catch (AnotherException $e) {
            doSomething();
        } finally {
            return true;
        }
    default:
        $other = $code;
        break;
}

// Okay - all - non-finally - branches have a terminating statement.
switch ( $a ) {
    case 1:
        try {
            return false;
        } catch (Exception $e) /*comment*/ {
            return true;
        }
        // Comment
        catch (AnotherException $e) {
            return true;
        } finally {
            doSomething();
        }
    default:
        $other = $code;
        break;
}

// Okay - finally will always be executed, so all branches are covered by the `return` in finally.
// Non-standard structure order.
switch ( $a ) {
    case 1:
        try {
            doSomething();
        } catch (Exception $e) {
            doSomething();
        } finally {
            return true;
        } catch (AnotherException $e) {
            doSomething();
        }
    default:
        $other = $code;
        break;
}

// Okay - all - non-finally - branches have a terminating statement.
// Non-standard structure order.
switch ( $a ) {
    case 1:
        try {
            return false;
        } finally {
            doSomething();
        } catch (MyException $e) {
            return true;
        } catch (AnotherException $e) {
            return true;
        }
    default:
        $other = $code;
        break;
}

// All okay, no finally. Any exception still uncaught will terminate the case anyhow, so we're good.
switch ( $a ) {
    case 1:
        try {
            return false;
        } catch (MyException $e) {
            return true;
        } catch (AnotherException $e) {
            return true;
        }
    default:
        $other = $code;
        break;
}

// All okay, no catch
switch ( $a ) {
    case 1:
        try {
            return true;
        } finally {
            doSomething();
        }
    case 2:
        $other = $code;
        break;
}

// All okay, try-catch nested in if.
switch ( $a ) {
    case 1:
        if ($a) {
            try {
                return true; // Comment.
            } catch (MyException $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            return true;
        }
    case 2:
        $other = $code;
        break;
}

// Missing fall-through comment.
switch ( $a ) {
    case 1:
        try {
            doSomething();
        } finally {
            doSomething();
        }
    case 2:
        $other = $code;
        break;
}

// Missing fall-through comment. One of the catches does not have a terminating statement.
switch ( $a ) {
    case 1:
        try {
            return false;
        } catch (Exception $e) {
            doSomething();
        } catch (AnotherException $e) {
            return true;
        } finally {
            doSomething();
        }
    default:
        $other = $code;
        break;
}

// Missing fall-through comment. Try does not have a terminating statement.
switch ( $a ) {
    case 1:
        try {
            doSomething();
        } finally {
            doSomething();
        } catch (Exception $e) {
            return true;
        } catch (AnotherException $e) {
            return true;
        }
    default:
        $other = $code;
        break;
}

// Missing fall-through comment. One of the catches does not have a terminating statement.
switch ( $a ) {
    case 1:
        try {
            return false;
        } catch (Exception $e) {
            doSomething();
        } catch (AnotherException $e) {
            return true;
        }
    default:
        $other = $code;
        break;
}

// Issue 3550 - comment after terminating statement.
switch (rand()) {
    case 1:
        if (rand() === 1) {
            break;
        } else {
            break; // comment
        }
    default:
        break;
}

// Fix: goto should be recognized as terminating statement.
switch ( $a ) {
    case 1:
        doSomething();
        goto jumpOut;
    default:
        $other = $code;
        break;
}

jumpOut:
doSomething();

// Fixable semicolon as case/default scope opener.
switch ($value) {
    case 'foo';
    case 'bar' /*comment*/ ;
    case 'baz'  ;
        echo 'foo, bar, or baz';
        break;
    default;
        echo 'Other';
    default ;
        echo 'Other';
}

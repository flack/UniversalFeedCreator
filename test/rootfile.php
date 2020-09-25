<?php
/**
 * PHP Unit bootstrapping
 * the used phpunit version depends on the available PHP version, newer versions are namespaced
 * whiler older ones are not.
 */

require dirname(__DIR__).'/vendor/autoload.php';

if (
    !class_exists('\PHPUnit_Framework_TestCase')
    && class_exists('\PHPUnit\Framework\TestCase')
) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
    class_alias('\PHPUnit\Framework\Constraint\IsEqual', '\PHPUnit_Framework_Constraint_IsEqual');
    if (class_exists('\PHPUnit\Util\InvalidArgumentHelper')) {
        class_alias('\PHPUnit\Util\InvalidArgumentHelper', '\PHPUnit_Util_InvalidArgumentHelper');
    }
}

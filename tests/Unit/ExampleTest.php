<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExampleTest::class)]
class ExampleTest extends TestCase
{
    #[Test]
    public function true_is_true(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function example_of_another_test(): void
    {
        $this->assertFalse(false);
    }
} 
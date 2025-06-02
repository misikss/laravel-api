<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ExampleTest::class)]
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    #[Test]
    public function the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}

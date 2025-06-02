<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    #[Test]
    public function user_can_be_created(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    #[Test]
    #[DataProvider('emailProvider')]
    public function email_validation($email, $shouldBeValid): void
    {
        $user = new User();
        $user->email = $email;

        $this->assertEquals($shouldBeValid, filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false);
    }

    public static function emailProvider(): array
    {
        return [
            'valid email' => ['test@example.com', true],
            'invalid email' => ['not-an-email', false],
            'another valid email' => ['user.name+tag@domain.com', true],
        ];
    }

    #[Test]
    public function create_user_for_dependency(): User
    {
        $user = new User([
            'name' => 'Dependency Test',
            'email' => 'dependency@test.com',
            'password' => 'password'
        ]);

        $this->assertInstanceOf(User::class, $user);
        return $user;
    }

    #[Test]
    #[Depends('create_user_for_dependency')]
    public function user_email_can_be_updated(User $user): void
    {
        $user->email = 'new@email.com';
        $this->assertEquals('new@email.com', $user->email);
    }
} 
<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    #[DataProvider('emailProvider')]
    public function email_validation($email, $shouldBeValid): void
    {
        $user = User::factory()->make();
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
        $user = User::factory()->create([
            'name' => 'Dependency Test',
            'email' => 'dependency@test.com',
            'password' => Hash::make('password')
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'dependency@test.com'
        ]);
        
        return $user;
    }

    #[Test]
    #[Depends('create_user_for_dependency')]
    public function user_email_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Update Test',
            'email' => 'old@email.com',
            'password' => Hash::make('password')
        ]);

        $newEmail = 'new@email.com';
        $user->email = $newEmail;
        $user->save();
        
        $this->assertEquals($newEmail, $user->email);
        $this->assertDatabaseHas('users', ['email' => $newEmail]);
        $this->assertDatabaseMissing('users', ['email' => 'old@email.com']);
    }
} 
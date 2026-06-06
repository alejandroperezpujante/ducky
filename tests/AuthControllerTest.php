<?php

declare(strict_types=1);

use App\Authentication\User;
use Tempest\Auth\Authentication\Authenticator;

it('shows the login form', function () {
    $this->database->setup();

    $this->http->get('/login')->assertOk()->assertSee('Sign In');
});

it('shows the register form', function () {
    $this->database->setup();

    $this->http->get('/register')->assertOk()->assertSee('Create Account');
});

it('redirects authenticated user away from login', function () {
    $this->database->setup();

    $user = User::create(email: 'auth@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->get('/login')->assertRedirect('/');
});

it('redirects authenticated user away from register', function () {
    $this->database->setup();

    $user = User::create(email: 'auth@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->get('/register')->assertRedirect('/');
});

it('registers a new user', function () {
    $this->database->setup();

    $this->http
        ->post('/register', ['email' => 'new@ducky.test', 'password' => 'password1234'])
        ->assertRedirect('/');

    $this->database->assertTableHasRow('users', email: 'new@ducky.test');
});

it('rejects duplicate email on register', function () {
    $this->database->setup();

    User::create(email: 'taken@ducky.test', password: 'password1234');

    $this->http
        ->post('/register', ['email' => 'taken@ducky.test', 'password' => 'password1234'])
        ->assertRedirect();

    expect(User::count()->execute())->toBe(1);
});

it('logs in with valid credentials', function () {
    $this->database->setup();

    User::create(email: 'login@ducky.test', password: 'password1234');

    $this->http
        ->post('/login', ['email' => 'login@ducky.test', 'password' => 'password1234'])
        ->assertRedirect('/');
});

it('rejects invalid credentials', function () {
    $this->database->setup();

    User::create(email: 'login@ducky.test', password: 'password1234');

    $this->http
        ->post('/login', ['email' => 'login@ducky.test', 'password' => 'wrongpassword'])
        ->assertRedirect();
});

it('rejects empty validation fields on register', function () {
    $this->database->setup();

    $this->http->post('/register', ['email' => '', 'password' => ''])->assertHasValidationError('email');
});

it('logs out and redirects to login', function () {
    $this->database->setup();

    $user = User::create(email: 'auth@ducky.test', password: 'password1234');
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->post('/logout')->assertRedirect('/login');
});

<?php

declare(strict_types=1);

use App\Accounts\User;
use Tempest\Auth\Authentication\Authenticator;

it('redirects unauthenticated users away from profile', function () {
    $this->database->setup();

    $this->http->get('/profile')->assertRedirect('/login');
});

it('shows profile page to authenticated users', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'profileuser',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->get('/profile')->assertOk()->assertSee('My Profile');
});

it('shows username on profile page', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'profileuser',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http->get('/profile')->assertOk()->assertSee('@profileuser');
});

it('updates display name', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'profileuser',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->patch('/profile/display-name', ['displayName' => 'My Display'])
        ->assertRedirect('/profile');

    $this->database->assertTableHasRow('users', displayName: 'My Display');
});

it('clears display name when blank', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'profileuser',
        displayName: 'Old Name',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->patch('/profile/display-name', ['displayName' => ''])
        ->assertRedirect('/profile');

    $updated = User::find(email: 'profile@ducky.test')->first();
    expect($updated->displayName)->toBeNull();
});

it('updates username', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'oldname',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->patch('/profile/username', ['username' => 'newname'])
        ->assertRedirect('/profile');

    $this->database->assertTableHasRow('users', username: 'newname');
});

it('rejects duplicate username on update', function () {
    $this->database->setup();

    User::create(
        email: 'other@ducky.test',
        password: 'password1234',
        username: 'taken',
    );
    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'mine',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->patch('/profile/username', ['username' => 'taken'])
        ->assertRedirect('/profile');

    $this->database->assertTableHasRow('users', username: 'mine');
});

it('rejects empty username on update', function () {
    $this->database->setup();

    $user = User::create(
        email: 'profile@ducky.test',
        password: 'password1234',
        username: 'profileuser',
    );
    $this->container->get(Authenticator::class)->authenticate($user);

    $this->http
        ->patch('/profile/username', ['username' => ''])
        ->assertHasValidationError('username');
});

<?php

declare(strict_types=1);

it('shows the profile placeholder', function () {
    $this->database->setup();

    $this->http->get('/profile')->assertOk()->assertSee('My Profile');
});

<?php

declare(strict_types=1);

it('shows the feed placeholder', function () {
    $this->database->setup();

    $this->http->get('/feed')->assertOk()->assertSee('My Feed');
});

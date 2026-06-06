<?php

declare(strict_types=1);

it('shows the messages placeholder', function () {
    $this->database->setup();

    $this->http->get('/messages')->assertOk()->assertSee('Messages');
});

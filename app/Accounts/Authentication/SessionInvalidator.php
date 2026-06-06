<?php

declare(strict_types=1);

namespace App\Accounts\Authentication;

use App\Accounts\User;
use Tempest\Auth\Authentication\SessionAuthenticator;
use Tempest\Http\Session\Managers\DatabaseSession;

use function Tempest\Database\query;

final readonly class SessionInvalidator
{
    /**
     * Invalidates (deletes) all sessions belonging to the given user.
     * This forces the user to re-authenticate on every device.
     */
    public function invalidateAllFor(User $user): void
    {
        $sessions = query(DatabaseSession::class)->select()->all();

        foreach ($sessions as $session) {
            $data = @unserialize($session->data);

            if (! is_array($data)) {
                continue;
            }

            $storedClass = $data[SessionAuthenticator::AUTHENTICATABLE_CLASS] ?? null;
            $storedId = $data[SessionAuthenticator::AUTHENTICATABLE_KEY] ?? null;

            if ($storedClass !== User::class) {
                continue;
            }

            if ((string) $storedId !== (string) $user->id->value) {
                continue;
            }

            query(DatabaseSession::class)
                ->delete()
                ->where('id', $session->id)
                ->execute();
        }
    }
}

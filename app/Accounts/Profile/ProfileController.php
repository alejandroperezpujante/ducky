<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use App\Accounts\Authentication\MustBeAuthenticated;
use App\Accounts\Authentication\SessionInvalidator;
use App\Accounts\User;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\File;
use Tempest\Http\Responses\NotFound;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Router\Get;
use Tempest\Router\Patch;
use Tempest\Router\Post as PostRoute;
use Tempest\Storage\Storage;
use Tempest\View\View;

use function Tempest\internal_storage_path;
use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class ProfileController
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Session $session,
        private Storage $storage,
        private SessionInvalidator $sessionInvalidator,
    ) {}

    #[Get('/profile', middleware: [MustBeAuthenticated::class])]
    public function show(): View
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        $avatarUrl = $user->avatarPath !== null
            ? uri([self::class, 'avatar'], user: $user->publicId)
            : null;

        return view(
            './profile.view.php',
            user: $user,
            avatarUrl: $avatarUrl,
            success: $this->session->get('success'),
            error: $this->session->get('error'),
        );
    }

    /**
     * Serve the user's avatar from local storage.
     * Public so browsers can load it without auth middleware.
     */
    #[Get('/profile/avatar/{user}')]
    public function avatar(User $user): File|NotFound
    {
        if ($user->avatarPath === null) {
            return new NotFound();
        }

        $absolutePath = internal_storage_path('avatars', $user->avatarPath);

        if (! file_exists($absolutePath)) {
            return new NotFound();
        }

        return new File($absolutePath);
    }

    #[PostRoute('/profile/avatar', middleware: [MustBeAuthenticated::class])]
    public function uploadAvatar(UploadAvatarRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        $upload = $request->files['avatar'] ?? null;

        if ($upload === null || $upload->getError() !== UPLOAD_ERR_OK) {
            $this->session->flash('error', 'No file was uploaded.');

            return new Back(uri([self::class, 'show']));
        }

        // Validate size (≤ 5 MB)
        if ($upload->getSize() > (5 * 1024 * 1024)) {
            $this->session->flash('error', 'Image must be 5 MB or smaller.');

            return new Back(uri([self::class, 'show']));
        }

        // Validate mime type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $upload->getClientMediaType();

        if (! in_array($mimeType, $allowedTypes, strict: true)) {
            $this->session->flash('error', 'Only JPEG, PNG, GIF, and WebP images are allowed.');

            return new Back(uri([self::class, 'show']));
        }

        // Derive extension from mime type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $extensions[$mimeType];
        $key = $user->publicId . '.' . $ext;

        // Delete old avatar if present and different key
        if ($user->avatarPath !== null && $user->avatarPath !== $key) {
            try {
                $this->storage->delete($user->avatarPath);
            } catch (\Throwable) {
                // Ignore; old file may already be gone
            }
        }

        $this->storage->writeStream($key, $upload->getStream());
        $user->update(avatarPath: $key);

        $this->session->flash('success', 'Profile picture updated.');

        return new Redirect(uri([self::class, 'show']));
    }

    #[Patch('/profile/display-name', middleware: [MustBeAuthenticated::class])]
    public function updateDisplayName(UpdateDisplayNameRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        $displayName = trim($request->displayName) ?: null;
        $user->update(displayName: $displayName);

        $this->session->flash('success', 'Display name updated.');

        return new Redirect(uri([self::class, 'show']));
    }

    #[Patch('/profile/username', middleware: [MustBeAuthenticated::class])]
    public function updateUsername(UpdateUsernameRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        $taken = User::find(username: $request->username)->first();

        if ($taken !== null && (string) $taken->id->value !== (string) $user->id->value) {
            $this->session->flash('error', 'That username is already taken.');

            return new Back(uri([self::class, 'show']));
        }

        $user->update(username: $request->username);

        $this->session->flash('success', 'Username updated.');

        return new Redirect(uri([self::class, 'show']));
    }

    #[PostRoute('/profile/report/request', middleware: [MustBeAuthenticated::class])]
    public function requestDataReport(ReportFormatRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();

        $format = in_array($request->format, ['json', 'xml', 'text'], strict: true)
            ? $request->format
            : 'json';

        // TODO: generate data-retention report as password-protected zip and email to $user->email
        // Format requested: $format
        // Planned: collect all user data, create ZipArchive with AES encryption, email as attachment

        $this->session->flash('success', 'Your data report has been requested. You will receive a password-protected zip file at ' . $user->email . ' within a few minutes.');

        return new Redirect(uri([self::class, 'show']));
    }
}

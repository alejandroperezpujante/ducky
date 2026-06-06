<?php

declare(strict_types=1);

/**
 * @var string      $formAction The signed POST URL (preserves signature + expires_at)
 * @var string|null $error      Flash error message
 */

$error ??= null;
?>

<x-base :title="'Change Password'" :active="'profile'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-balance">Change Password</h1>
                    <p class="mt-3 text-ink-500 text-sm">Confirm your current password and choose a new one. All active sessions will be signed out after the change.</p>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-md mx-auto bg-white rounded-bubble shadow-soft p-8 flex flex-col gap-6">

                    <div :if="$error !== null" class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        {{ $error }}
                    </div>

                    <x-form :action="$formAction" :method="'POST'">
                        <div class="flex flex-col gap-4">
                            <x-input name="currentPassword" type="password" :label="'Current password'"/>
                            <x-input name="newPassword" type="password" :label="'New password (min. 8 characters)'"/>
                            <x-input name="newPasswordConfirmation" type="password" :label="'Confirm new password'"/>
                            <x-submit :label="'Update Password'"/>
                        </div>
                    </x-form>

                    <p class="text-center text-sm text-ink-500">
                        Changed your mind? <a href="/profile" class="text-coral-500 font-semibold hover:underline">Back to profile</a>
                    </p>

                </div>
            </section>

        </div>
    </main>
</x-base>

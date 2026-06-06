<?php

/**
 * @var string      $formAction The signed POST URL (preserves signature + expires_at)
 * @var string|null $error      Flash error message
 */

$error ??= null;
?>

<x-base :title="'Change Email'" :active="'profile'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-balance">Change Email</h1>
                    <p class="mt-3 text-ink-500 text-sm">Enter your current password and your new email address to complete the change. All active sessions will be signed out.</p>
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
                            <x-input name="newEmail" type="email" :label="'New email address'"/>
                            <x-submit :label="'Update Email'"/>
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

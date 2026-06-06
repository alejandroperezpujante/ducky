<?php

declare(strict_types=1);

/**
 * @var string      $formAction The signed POST URL (preserves signature + expires_at)
 * @var string|null $error      Flash error message
 */

$error ??= null;
?>

<x-base :title="'Confirm Account Deletion'" :active="'profile'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                            <x-icon :name="'tabler:alert-triangle'" :class="'w-8 h-8 text-red-600'"/>
                        </div>
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-balance text-red-700">Delete Account</h1>
                    <p class="mt-3 text-ink-500 text-sm">This action cannot be undone.</p>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-md mx-auto bg-white rounded-bubble shadow-soft p-8 flex flex-col gap-6 border border-red-100">

                    <div :if="$error !== null" class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        {{ $error }}
                    </div>

                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-4 text-sm text-amber-800">
                        <p class="font-semibold mb-1">What happens next:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Your account will be scheduled for deletion in <strong>30 days</strong>.</li>
                            <li>You'll receive a confirmation email.</li>
                            <li>All your data — posts, messages, and profile — will be permanently removed after the grace period.</li>
                            <li>You can cancel by signing back in before the 30-day period ends.</li>
                        </ul>
                    </div>

                    <x-form :action="$formAction" :method="'POST'">
                        <div class="flex flex-col gap-4">
                            <x-input name="currentPassword" type="password" :label="'Enter your password to confirm'"/>
                            <input
                                type="submit"
                                value="Yes, Delete My Account"
                                class="w-full rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-4 text-sm cursor-pointer transition-colors"
                            />
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

<?php

/**
 * @var \App\Authentication\User $user
 * @var string|null $avatarUrl  Absolute URL to the avatar image, or null
 * @var string|null $success Flash success message
 * @var string|null $error   Flash error message
 */

use App\Profile\AccountDeletionController;
use App\Profile\EmailChangeController;
use App\Profile\PasswordChangeController;
use App\Profile\ProfileController;

use function Tempest\Router\uri;

$success ??= null;
$error ??= null;
$avatarUrl ??= null;
?>

<x-base :title="'My Profile'" :active="'profile'">
    <main class="w-full">
        <div class="isolate">

            {{-- Page heading --}}
            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-8 text-center">
                <div class="max-w-2xl w-full">

                    {{-- Avatar display --}}
                    <div class="flex justify-center mb-4">
                        <img
                            :if="$avatarUrl !== null"
                            src="<?= htmlspecialchars($avatarUrl) ?>"
                            alt="Profile picture"
                            class="w-24 h-24 rounded-full object-cover shadow-soft border-4 border-white"
                        />

                        <div :else class="w-24 h-24 rounded-full bg-cream-100 border-4 border-white shadow-soft flex items-center justify-center">
                            <x-icon :name="'tabler:user'" :class="'w-12 h-12 text-ink-300'"/>
                        </div>
                    </div>

                    <h1 class="text-4xl sm:text-5xl font-extrabold text-ink-900 text-balance">
                        {{ $user->name() !== '' ? $user->name() : 'My Profile' }}
                    </h1>
                    <p :if="$user->username !== null" class="mt-1 text-ink-400 text-sm">@{{ $user->username }}</p>
                </div>
            </section>

            {{-- Flash messages --}}
            <section class="px-6 pb-4">
                <div class="max-w-2xl mx-auto">
                    <x-flash :success="$success" :error="$error"/>
                </div>
            </section>

            {{-- Profile cards --}}
            <section class="px-6 pb-16">
                <div class="max-w-2xl mx-auto flex flex-col gap-6">

                    {{-- ── Profile picture ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Profile Picture</h2>
                        <p class="text-sm text-ink-500">Upload a JPEG, PNG, GIF, or WebP image. Maximum 5 MB.</p>

                        <x-form
                            :action="uri([ProfileController::class, 'uploadAvatar'])"
                            :method="'POST'"
                            :enctype="'multipart/form-data'"
                        >
                            <div class="flex flex-col gap-4">
                                <x-file name="avatar" :label="'Choose image'" :accept="'image/*'" :hint="'Max 5 MB — JPEG, PNG, GIF, WebP'"/>
                                <x-submit :label="'Upload Picture'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Display name ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Display Name</h2>
                        <p class="text-sm text-ink-500">Shown instead of your username when set. Leave blank to use your username.</p>

                        <x-form :action="uri([ProfileController::class, 'updateDisplayName'])" :method="'PATCH'">
                            <div class="flex flex-col gap-4">
                                <x-input
                                    name="displayName"
                                    :label="'Display name'"
                                    :default="$user->displayName ?? ''"
                                />
                                <x-submit :label="'Save Display Name'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Username ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Username</h2>
                        <p class="text-sm text-ink-500">Your unique handle. Must be different from all other users.</p>

                        <x-form :action="uri([ProfileController::class, 'updateUsername'])" :method="'PATCH'">
                            <div class="flex flex-col gap-4">
                                <x-input
                                    name="username"
                                    :label="'Username'"
                                    :default="$user->username ?? ''"
                                />
                                <x-submit :label="'Save Username'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Email ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Email Address</h2>

                        <div class="flex items-center gap-3 p-3 bg-cream-50 rounded-xl border border-cream-200">
                            <x-icon :name="'tabler:mail'" :class="'w-5 h-5 text-ink-400 shrink-0'"/>
                            <span class="text-sm text-ink-700 font-medium">{{ $user->email }}</span>
                        </div>

                        <p class="text-sm text-ink-500">
                            To change your email, confirm your current password below. We'll send a confirmation link to your <em>current</em> address.
                        </p>

                        <x-form :action="uri([EmailChangeController::class, 'requestChange'])" :method="'POST'">
                            <div class="flex flex-col gap-4">
                                <x-input name="currentPassword" type="password" :label="'Current password'"/>
                                <x-submit :label="'Send Email Change Link'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Password ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Password</h2>
                        <p class="text-sm text-ink-500">
                            Confirm your current password to receive a secure link to set a new one.
                            All active sessions will be signed out after the change.
                        </p>

                        <x-form :action="uri([PasswordChangeController::class, 'requestChange'])" :method="'POST'">
                            <div class="flex flex-col gap-4">
                                <x-input name="currentPassword" type="password" :label="'Current password'"/>
                                <x-submit :label="'Send Password Change Link'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Data report ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4">
                        <h2 class="text-lg font-bold text-ink-900">Data Retention Report</h2>
                        <p class="text-sm text-ink-500">
                            Download a copy of all data we hold about you, delivered as a password-protected zip to your email address.
                        </p>

                        <x-form :action="uri([ProfileController::class, 'requestDataReport'])" :method="'POST'">
                            <div class="flex flex-col gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-ink-700 mb-2">Report format</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="format" value="json" checked class="accent-coral-500"/>
                                            <span class="text-sm text-ink-700">JSON</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="format" value="xml" class="accent-coral-500"/>
                                            <span class="text-sm text-ink-700">XML</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="format" value="text" class="accent-coral-500"/>
                                            <span class="text-sm text-ink-700">Plain text</span>
                                        </label>
                                    </div>
                                </div>
                                <x-submit :label="'Request My Data'"/>
                            </div>
                        </x-form>
                    </div>

                    {{-- ── Danger zone ── --}}
                    <div class="bg-white rounded-bubble shadow-soft p-6 flex flex-col gap-4 border border-red-100">
                        <h2 class="text-lg font-bold text-red-700">Danger Zone</h2>
                        <p class="text-sm text-ink-500">
                            Once you request account deletion, you have <strong>30 days</strong> before your account and all associated data are permanently removed. You'll receive a confirmation email with a secure link to finalise the request.
                        </p>

                        <x-form :action="uri([AccountDeletionController::class, 'requestDeletion'])" :method="'POST'">
                            <div class="flex flex-col gap-4">
                                <x-input name="currentPassword" type="password" :label="'Confirm your password to continue'"/>
                                <input
                                    type="submit"
                                    value="Delete My Account"
                                    class="w-full rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-4 text-sm cursor-pointer transition-colors"
                                />
                            </div>
                        </x-form>

                        <div class="border-t border-cream-200 pt-4">
                            <x-form :action="'/logout'" :method="'POST'">
                                <x-submit :label="'Sign Out'"/>
                            </x-form>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </main>
</x-base>

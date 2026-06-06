<?php

/** @var string|null $error Flash error message from the previous request */
$error ??= null;
?>

<x-base :title="'Sign In'" :active="'identify'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-4xl sm:text-6xl text-balance">Sign In</h1>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-md mx-auto bg-white rounded-bubble shadow-soft p-8 flex flex-col gap-6">

                    <div :if="$error !== null" class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        {{ $error }}
                    </div>

                    <x-form :action="'/login'" :method="'POST'">
                        <div class="flex flex-col gap-4">
                            <x-input name="email" type="email" :label="'Email'"/>
                            <x-input name="password" type="password" :label="'Password'"/>
                            <x-submit :label="'Sign In'"/>
                        </div>
                    </x-form>

                    <p class="text-center text-sm text-ink-500">
                        No account?
                        <a href="/register" class="text-coral-500 font-semibold hover:underline">Create one</a>
                    </p>

                </div>
            </section>

        </div>
    </main>
</x-base>

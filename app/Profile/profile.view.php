<?php

?>

<x-base :title="'My Profile'" :active="'profile'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-4xl sm:text-6xl text-balance">My Profile</h1>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-2xl mx-auto bg-white rounded-bubble shadow-soft p-8 flex flex-col items-center gap-3 text-center">
                    <x-icon :name="'tabler:user'" :class="'w-10 h-10 text-coral-300'"/>
                    <p class="text-ink-500 text-sm">Profile features are coming soon.</p>
                </div>
            </section>

        </div>
    </main>
</x-base>

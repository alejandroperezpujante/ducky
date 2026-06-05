<?php

/**
 * @var string $action
 */

?>

<x-base :title="'New Post'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-24 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-6xl sm:text-7xl text-balance">New Post</h1>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-2xl mx-auto bg-white rounded-bubble shadow-soft p-6">
                    <x-post-form :action="$action" :submit-label="'Create post'"/>
                </div>
                <div class="max-w-2xl mx-auto mt-4 text-center">
                    <a href="/" class="text-sm text-ink-400 hover:text-ink-600 transition-colors">← Back to posts</a>
                </div>
            </section>

        </div>
    </main>
</x-base>

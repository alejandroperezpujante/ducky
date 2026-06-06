<?php

/**
 * @var \App\Post $post
 * @var string $action
 */

?>

<x-base :title="'Edit Post'" :active="'create'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-10 sm:pt-16 pb-10 text-center">
                <div class="max-w-2xl w-full">
                    <h1 class="text-4xl sm:text-6xl text-balance">Edit Post</h1>
                    <p class="mt-2 text-xs text-ink-400 font-mono">{{ $post->slug }}</p>
                </div>
            </section>

            <section class="px-6 pb-12">
                <div class="max-w-2xl mx-auto bg-white rounded-bubble shadow-soft p-6">
                    <x-post-form
                        :action="$action"
                        :method="'PATCH'"
                        :submit-label="'Save changes'"
                    />
                </div>
                <div class="max-w-2xl mx-auto mt-4 text-center">
                    <a href="/" class="text-sm text-ink-400 hover:text-ink-600 transition-colors">← Back to posts</a>
                </div>
            </section>

        </div>
    </main>
</x-base>

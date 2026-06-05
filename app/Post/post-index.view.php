<?php

/**
 * @var \Tempest\Support\Paginator\PaginatedData $posts
 * Each item in $posts->data is an array with keys: post, editUrl, deleteUrl
 */

?>

<x-base :title="'Posts'">
    <main class="w-full">
        <div class="isolate">

            <section class="flex flex-col items-center px-6 pt-24 pb-10 text-center">
                <div class="max-w-2xl w-full flex items-center justify-between">
                    <h1 class="text-6xl sm:text-7xl text-balance">Posts</h1>
                    <a href="/posts/create" class="inline-flex items-center gap-1.5 rounded-bubble bg-coral-400 px-4 py-2 text-sm font-bold text-white shadow-soft hover:bg-coral-500 transition-colors">
                        New post
                    </a>
                </div>
            </section>

            <section class="px-6 pb-12">
                <ul class="max-w-2xl mx-auto grid gap-3 list-none p-0 m-0">

                    <li :foreach="$posts->data as $row" class="bg-white rounded-bubble shadow-soft p-4 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex flex-col gap-1 min-w-0">
                                <span class="text-xs text-ink-400 font-mono">#{{ $row['post']->id }} · {{ $row['post']->slug }}</span>
                                <p class="text-sm text-ink-800 leading-relaxed">{{ $row['post']->content }}</p>
                                <span class="text-xs text-ink-400">{{ $row['post']->createdAt }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a :href="$row['editUrl']" class="text-xs font-semibold text-teal-600 hover:text-teal-700 transition-colors">
                                    Edit
                                </a>
                                <x-form :action="$row['deleteUrl']" :method="'DELETE'">
                                    <x-submit label="Delete"/>
                                </x-form>
                            </div>
                        </div>
                    </li>

                    <li :forelse class="bg-white rounded-bubble shadow-soft p-4 text-center text-ink-400 text-sm">
                        No posts yet — <a href="/posts/create" class="text-coral-500 hover:underline">create the first one</a>.
                    </li>

                </ul>

                <nav :if="$posts->totalPages > 1" class="max-w-2xl mx-auto mt-6 flex items-center justify-center gap-2">
                    <a
                        :if="$posts->hasPrevious"
                        :href="'/?page=' . $posts->previousPage"
                        class="rounded-bubble bg-white shadow-soft px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50 transition-colors"
                    >
                        ← Prev
                    </a>
                    <span class="text-sm text-ink-500">
                        Page {{ $posts->currentPage }} / {{ $posts->totalPages }}
                    </span>
                    <a
                        :if="$posts->hasNext"
                        :href="'/?page=' . $posts->nextPage"
                        class="rounded-bubble bg-white shadow-soft px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50 transition-colors"
                    >
                        Next →
                    </a>
                </nav>
            </section>

        </div>
    </main>
</x-base>

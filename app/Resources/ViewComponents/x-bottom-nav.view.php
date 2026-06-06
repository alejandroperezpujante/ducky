<?php

/**
 * @var string|null $active One of: 'home', 'feed', 'create', 'messages', 'profile'
 */

$active ??= null;

$base = 'flex flex-col items-center gap-0.5 py-3 flex-1 text-xs font-semibold transition-colors';
$homeClass = $base . ($active === 'home' ? ' text-coral-500' : ' text-ink-400');
$feedClass = $base . ($active === 'feed' ? ' text-coral-500' : ' text-ink-400');
$messagesClass = $base . ($active === 'messages' ? ' text-coral-500' : ' text-ink-400');
$profileClass = $base . ($active === 'profile' ? ' text-coral-500' : ' text-ink-400');
?>

<nav class="fixed bottom-0 inset-x-0 z-50" aria-label="Main navigation">
    <div class="bg-white/90 backdrop-blur-md border-t border-cream-200 pb-[env(safe-area-inset-bottom,0px)]">
        <div class="max-w-2xl mx-auto flex items-end justify-around">

            <a href="/" :class="$homeClass">
                <x-icon :name="'tabler:home'" :class="'w-6 h-6'"/>
                <span>Home</span>
            </a>

            <a href="/feed" :class="$feedClass">
                <x-icon :name="'tabler:rss'" :class="'w-6 h-6'"/>
                <span>My Feed</span>
            </a>

            <a href="/posts/create" class="flex flex-col items-center gap-1 flex-1 -mt-5 pb-1 text-xs font-semibold text-coral-500 transition-colors" aria-label="New post">
                <span class="w-14 h-14 bg-coral-400 rounded-full flex items-center justify-center shadow-pop text-white">
                    <x-icon :name="'tabler:plus'" :class="'w-7 h-7'"/>
                </span>
                <span>New Post</span>
            </a>

            <a href="/messages" :class="$messagesClass">
                <x-icon :name="'tabler:message'" :class="'w-6 h-6'"/>
                <span>Messages</span>
            </a>

            <a href="/profile" :class="$profileClass">
                <x-icon :name="'tabler:user'" :class="'w-6 h-6'"/>
                <span>Profile</span>
            </a>

        </div>
    </div>
</nav>

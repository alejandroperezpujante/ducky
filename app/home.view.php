<x-base :title="Tempest\env('APP_TITLE', default: 'Tempest Scaffold')">
  <main class="w-full">
    <div class="isolate">

      <!-- Hero -->
      <section class="flex flex-col items-center px-6 pt-24 pb-10 text-center">
        <div class="max-w-sm w-full">
          <h1 class="text-6xl sm:text-7xl text-balance">Tempest</h1>
          <p class="mt-4 text-ink-600 text-lg sm:text-xl leading-relaxed text-pretty font-medium">
            The PHP framework that gets out of your way.
          </p>
          <nav class="flex flex-col sm:flex-row justify-center items-center gap-3 mt-8">
            <a
                href="https://tempestphp.com/docs"
                target="_blank"
                class="px-6 py-3 bg-yellow-300 hover:bg-yellow-400 text-ink-900 font-bold rounded-full shadow-soft text-sm w-full sm:w-auto"
            >
              Documentation
            </a>
            <a
                href="https://tempestphp.com/discord"
                class="px-6 py-3 bg-teal-100 hover:bg-teal-200 text-teal-600 font-bold rounded-full text-sm w-full sm:w-auto"
            >
              Join our Discord →
            </a>
          </nav>
        </div>
      </section>

      <!-- Palette showcase cards -->
      <section class="px-6 pb-12">
        <ul class="max-w-sm mx-auto grid grid-cols-3 gap-3 list-none p-0 m-0">

          <!-- Coral -->
          <li class="bg-white rounded-bubble shadow-soft p-4 flex flex-col gap-2">
            <span class="w-9 h-9 rounded-full bg-coral-100 flex items-center justify-center">
              <span class="text-coral-500 text-base font-bold">♥</span>
            </span>
            <h2 class="font-bold text-ink-800 text-sm">Coral</h2>
            <p class="text-xs text-ink-400 leading-snug">Warm &amp; cozy</p>
          </li>

          <!-- Teal -->
          <li class="bg-white rounded-bubble shadow-pop p-4 flex flex-col gap-2">
            <span class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center">
              <span class="text-teal-500 text-base font-bold">✦</span>
            </span>
            <h2 class="font-bold text-ink-800 text-sm">Teal</h2>
            <p class="text-xs text-ink-400 leading-snug">Fresh &amp; cool</p>
          </li>

          <!-- Yellow -->
          <li class="bg-white rounded-bubble shadow-soft p-4 flex flex-col gap-2">
            <span class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center">
              <span class="text-yellow-500 text-base font-bold">★</span>
            </span>
            <h2 class="font-bold text-ink-800 text-sm">Yellow</h2>
            <p class="text-xs text-ink-400 leading-snug">Bright &amp; fun</p>
          </li>

        </ul>
      </section>

    </div>
  </main>
</x-base>

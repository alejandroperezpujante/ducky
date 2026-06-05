<x-base :title="'Books'">
  <main class="w-full">
    <div class="isolate">

      <section class="flex flex-col items-center px-6 pt-24 pb-10 text-center">
        <div class="max-w-sm w-full">
          <h1 class="text-6xl sm:text-7xl text-balance">Books</h1>
          <p class="mt-4 text-ink-600 text-lg sm:text-xl leading-relaxed text-pretty font-medium">
            Stored in SQLite, served by Tempest.
          </p>
        </div>
      </section>

      <section class="px-6 pb-12">
        <ul class="max-w-sm mx-auto grid gap-3 list-none p-0 m-0">

          <li :foreach="$books as $book" class="bg-white rounded-bubble shadow-soft p-4 flex flex-col gap-1">
            <span class="text-xs text-ink-400 font-mono">#{{ $book->id }}</span>
            <h2 class="font-bold text-ink-800 text-sm">{{ $book->title }}</h2>
          </li>

          <li :forelse class="bg-white rounded-bubble shadow-soft p-4 text-center text-ink-400 text-sm">
            No books yet — run <code class="font-mono text-xs">php tempest db:seed</code>.
          </li>

        </ul>
      </section>

    </div>
  </main>
</x-base>

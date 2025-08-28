<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-fuchsia-500 via-violet-500 to-sky-500 text-black rounded-md font-semibold text-xs uppercase tracking-widest hover:from-fuchsia-400 hover:via-violet-400 hover:to-sky-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-gray-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

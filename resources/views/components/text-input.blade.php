@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-900/70 text-white placeholder-white/40 border-white/10 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm']) }}>

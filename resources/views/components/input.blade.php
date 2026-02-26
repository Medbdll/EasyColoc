@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-xl shadow-sm bg-white/80 backdrop-blur-sm transition-all duration-200 px-4 py-3 text-gray-900 placeholder-gray-500 focus:bg-white']) !!}>

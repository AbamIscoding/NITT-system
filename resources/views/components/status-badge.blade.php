@props(['status'])

@php
    $status = strtolower($status);

    $colors = [
        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
        'paid' => 'bg-green-100 text-green-700 border-green-300',
        'cancelled' => 'bg-red-100 text-red-700 border-red-300',
    ];

    $class = $colors[$status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
@endphp

<span class="px-2 py-1 text-xs font-semibold rounded border {{ $class }}">
    {{ ucfirst($status) }}
</span>

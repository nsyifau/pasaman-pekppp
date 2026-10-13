@props(['id', 'title', 'description', 'action', 'method' => 'DELETE', 'label' => 'Hapus', 'tone' => 'destructive'])

@php
    $buttonVariant = in_array($tone, ['destructive', 'primary'], true) ? $tone : 'destructive';
    $verb = strtoupper($method);
@endphp
<x-ui.modal :id="$id" :title="$title" :description="$description" :icon="$buttonVariant === 'destructive' ? 'trash' : 'info'" :tone="$buttonVariant === 'destructive' ? 'danger' : 'info'" size="sm">
    {{ $slot }}
    <x-slot:footer>
        <x-ui.button variant="outline" data-modal-close>Batal</x-ui.button>
        <form action="{{ $action }}" method="POST">
            @csrf
            @if($verb !== 'POST') @method($verb) @endif
            <x-ui.button type="submit" :variant="$buttonVariant">{{ $label }}</x-ui.button>
        </form>
    </x-slot:footer>
</x-ui.modal>

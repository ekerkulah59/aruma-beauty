<x-filament::widget>
    <x-filament::card>
        <h3>Status Breakdown</h3>
        <ul>
            @foreach($counts as $status => $count)
                <li>
                    {{ ucfirst($status) }}: {{ $count }}
                    ({{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%)
                </li>
            @endforeach
        </ul>
    </x-filament::card>
</x-filament::widget>

@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
@endphp

<x-layouts.admin title="Periode Penilaian" group="Master Data">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-ui.page-header title="Periode Penilaian"
            description="Buka, tutup, atau kunci periode penilaian tahunan. Hanya satu periode dapat dibuka pada satu waktu." />
        <button type="button" class="btn btn-primary" data-modal-open="#createPeriodModal">
            <x-ui.icon name="plus" class="h-4 w-4" /> Buka Periode Baru
        </button>
    </div>

    @if ($currentPeriod)
        <x-ui.alert variant="info" title="Periode Aktif: {{ $currentPeriod->year }}">
            Unit layanan dapat mengisi F-01 untuk tahun <strong>{{ $currentPeriod->year }}</strong>.
            @if ($currentPeriod->opened_at)
                Dibuka pada {{ $currentPeriod->opened_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}.
            @endif
        </x-ui.alert>
    @else
        <x-ui.alert variant="warning" title="Tidak Ada Periode Terbuka">
            Unit layanan tidak dapat mengisi F-01 sampai Anda membuka sebuah periode penilaian.
        </x-ui.alert>
    @endif

    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">Daftar Periode</h2>
                <p class="card-desc mt-1.5">{{ $activeOpdCount }} unit layanan aktif menjadi acuan pembuatan sesi.</p>
            </div>
        </div>

        <div class="overflow-x-auto border-t">
            <table class="table min-w-[920px]">
                <thead>
                    <tr>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Sesi Unit</th>
                        <th class="text-center">Unit Dinilai</th>
                        <th>Catatan</th>
                        <th class="hidden lg:table-cell">Jejak Waktu</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        @php $stat = $stats[$period->id] ?? ['units' => 0, 'scored' => 0, 'active_opd' => $activeOpdCount]; @endphp
                        <tr>
                            <td class="text-center text-base font-semibold tabular-nums">{{ $period->year }}</td>
                            <td class="text-center">
                                <x-ui.badge :variant="$tone(\App\Models\EvaluationPeriod::statusTone($period->status))" class="badge-kategori">
                                    {{ $statuses[$period->status] ?? $period->status }}
                                </x-ui.badge>
                            </td>
                            <td class="text-center tabular-nums">{{ $stat['units'] }}/{{ $stat['active_opd'] }}</td>
                            <td class="text-center tabular-nums">{{ $stat['scored'] }}</td>
                            <td class="max-w-[16rem] text-sm text-muted-foreground">{{ $period->notes ?: '—' }}</td>
                            <td class="hidden lg:table-cell text-xs text-muted-foreground">
                                @if ($period->opened_at)<div>Dibuka: {{ $period->opened_at->format('d/m/Y H:i') }}</div>@endif
                                @if ($period->closed_at)<div>Ditutup: {{ $period->closed_at->format('d/m/Y H:i') }}</div>@endif
                                @if ($period->locked_at)<div>Dikunci: {{ $period->locked_at->format('d/m/Y H:i') }}</div>@endif
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($period->isOpen())
                                        <button type="button" class="btn btn-warning btn-sm" data-modal-open="#closePeriodModal{{ $period->id }}">
                                            <x-ui.icon name="lock" class="h-4 w-4" /> Tutup
                                        </button>
                                        <button type="button" class="btn btn-destructive btn-sm" data-modal-open="#lockPeriodModal{{ $period->id }}">
                                            <x-ui.icon name="shield" class="h-4 w-4" /> Kunci
                                        </button>
                                    @elseif ($period->isClosed())
                                        <button type="button" class="btn btn-success btn-sm" data-modal-open="#reopenPeriodModal{{ $period->id }}">
                                            <x-ui.icon name="refresh" class="h-4 w-4" /> Buka Kembali
                                        </button>
                                        <button type="button" class="btn btn-destructive btn-sm" data-modal-open="#lockPeriodModal{{ $period->id }}">
                                            <x-ui.icon name="shield" class="h-4 w-4" /> Kunci
                                        </button>
                                    @else
                                        <span class="text-xs text-muted-foreground">Read-only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-ui.empty icon="calendar" title="Belum ada periode"
                                    description="Buka periode penilaian pertama untuk memulai." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat aktivitas --}}
    <x-ui.card title="Riwayat Aktivitas Periode" description="Jejak audit pembukaan, penutupan, penguncian, dan pembukaan kembali.">
        @php $logs = $periods->flatMap->logs->sortByDesc('created_at')->take(20); @endphp
        @if ($logs->isEmpty())
            <p class="text-sm text-muted-foreground">Belum ada aktivitas.</p>
        @else
            <ul class="space-y-2">
                @foreach ($logs as $log)
                    <li class="flex flex-wrap items-center gap-2 rounded-md border bg-muted/30 px-3 py-2 text-sm">
                        <x-ui.badge variant="outline">{{ $log->period?->year }}</x-ui.badge>
                        <span class="font-medium">{{ $logActions[$log->action] ?? $log->action }}</span>
                        <span class="text-muted-foreground">oleh {{ $log->user?->name ?? 'Sistem' }}</span>
                        <span class="text-xs text-muted-foreground">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        @if ($log->reason)<span class="text-xs italic text-muted-foreground">— {{ $log->reason }}</span>@endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-ui.card>

    {{-- Modal buka periode --}}
    <x-ui.modal id="createPeriodModal" title="Buka Periode Penilaian" icon="calendar" tone="success"
        description="Sistem akan membuat sesi evaluasi draft untuk semua unit layanan aktif pada tahun terpilih.">
        <form method="POST" action="{{ route('periode.store') }}" autocomplete="off">
            @csrf
            <div class="space-y-4">
                <x-ui.field label="Tahun Periode" name="create_year" required>
                    <x-ui.input name="year" type="number" min="2000" max="2100" value="{{ date('Y') }}" required />
                </x-ui.field>
                <x-ui.field label="Catatan" name="create_notes">
                    <x-ui.textarea name="notes" rows="2" placeholder="Catatan pembukaan periode (opsional)..."></x-ui.textarea>
                </x-ui.field>
            </div>
            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <x-ui.button type="submit" variant="success"><x-ui.icon name="check" class="h-4 w-4" /> Buka Periode</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    @foreach ($periods as $period)
        @if ($period->isOpen())
            <x-ui.modal id="closePeriodModal{{ $period->id }}" title="Tutup Periode {{ $period->year }}" icon="lock" tone="warning" size="sm"
                description="Unit layanan tidak dapat lagi mengisi F-01. Admin masih dapat melakukan penilaian F-02/F-03.">
                <form method="POST" action="{{ route('periode.close', $period) }}" autocomplete="off">
                    @csrf
                    <x-ui.field label="Catatan (opsional)" name="close_reason_{{ $period->id }}">
                        <x-ui.textarea name="reason" rows="2" placeholder="Alasan/catatan penutupan..."></x-ui.textarea>
                    </x-ui.field>
                    <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                        <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                        <x-ui.button type="submit" variant="warning"><x-ui.icon name="lock" class="h-4 w-4" /> Tutup Periode</x-ui.button>
                    </div>
                </form>
            </x-ui.modal>

            <x-ui.modal id="lockPeriodModal{{ $period->id }}" title="Kunci Periode {{ $period->year }}" icon="shield" tone="danger" size="sm"
                description="Seluruh data tahun ini menjadi read-only dan tidak dapat diubah lagi (arsip final).">
                <form method="POST" action="{{ route('periode.lock', $period) }}" autocomplete="off">
                    @csrf
                    <x-ui.field label="Catatan (opsional)" name="lock_reason_{{ $period->id }}">
                        <x-ui.textarea name="reason" rows="2" placeholder="Alasan penguncian..."></x-ui.textarea>
                    </x-ui.field>
                    <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                        <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                        <x-ui.button type="submit" variant="destructive"><x-ui.icon name="shield" class="h-4 w-4" /> Kunci Periode</x-ui.button>
                    </div>
                </form>
            </x-ui.modal>
        @elseif ($period->isClosed())
            <x-ui.modal id="reopenPeriodModal{{ $period->id }}" title="Buka Kembali Periode {{ $period->year }}" icon="refresh" tone="info" size="sm"
                description="Periode akan dibuka kembali sehingga unit layanan dapat mengubah F-01. Alasan wajib diisi.">
                <form method="POST" action="{{ route('periode.reopen', $period) }}" autocomplete="off">
                    @csrf
                    <x-ui.field label="Alasan Pembukaan Kembali" name="reopen_reason_{{ $period->id }}" required>
                        <x-ui.textarea name="reason" rows="3" required placeholder="Contoh: perbaikan data hasil verifikasi..."></x-ui.textarea>
                    </x-ui.field>
                    <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                        <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                        <x-ui.button type="submit" variant="info"><x-ui.icon name="refresh" class="h-4 w-4" /> Buka Kembali</x-ui.button>
                    </div>
                </form>
            </x-ui.modal>

            <x-ui.modal id="lockPeriodModal{{ $period->id }}" title="Kunci Periode {{ $period->year }}" icon="shield" tone="danger" size="sm"
                description="Seluruh data tahun ini menjadi read-only dan tidak dapat diubah lagi (arsip final).">
                <form method="POST" action="{{ route('periode.lock', $period) }}" autocomplete="off">
                    @csrf
                    <x-ui.field label="Catatan (opsional)" name="lock_reason2_{{ $period->id }}">
                        <x-ui.textarea name="reason" rows="2" placeholder="Alasan penguncian..."></x-ui.textarea>
                    </x-ui.field>
                    <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                        <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                        <x-ui.button type="submit" variant="destructive"><x-ui.icon name="shield" class="h-4 w-4" /> Kunci Periode</x-ui.button>
                    </div>
                </form>
            </x-ui.modal>
        @endif
    @endforeach
</x-layouts.admin>

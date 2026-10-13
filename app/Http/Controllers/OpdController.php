<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OpdController extends Controller
{
    /**
     * Daftar unit layanan beserta form tambah/ubah (modal).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $category = (string) $request->query('category', '');
        $status = (string) $request->query('status', '');

        $query = Opd::query()->withCount(['users', 'evaluations']);

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if (in_array($category, array_keys(Opd::categories()), true)) {
            $query->where('category', $category);
        }

        if ($status === 'active' || $status === 'inactive') {
            $query->where('is_active', $status === 'active');
        }

        return view('opd.index', [
            'opds' => $query->orderBy('name')->paginate(10)->withQueryString(),
            'categories' => Opd::categories(),
            'search' => $search,
            'category' => $category,
            'status' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Opd::create($data);

        return redirect()
            ->route('opds.index')
            ->with('success', 'Unit layanan "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Opd $opd): RedirectResponse
    {
        $data = $this->validated($request, $opd);

        $opd->update($data);

        return redirect()
            ->route('opds.index')
            ->with('success', 'Unit layanan "' . $opd->name . '" berhasil diperbarui.');
    }

    public function destroy(Opd $opd): RedirectResponse
    {
        $name = $opd->name;

        if ($opd->evaluations()->exists()) {
            return redirect()
                ->route('opds.index')
                ->with('error', 'Unit layanan "' . $name . '" tidak dapat dihapus karena masih memiliki data evaluasi.');
        }

        $opd->delete();

        return redirect()
            ->route('opds.index')
            ->with('success', 'Unit layanan "' . $name . '" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Opd $opd = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Opd::categories()))],
        ], [
            'name.required' => 'Nama unit layanan wajib diisi.',
            'category.required' => 'Kategori unit wajib dipilih.',
            'category.in' => 'Kategori unit tidak dikenal.',
        ]);

        // Checkbox tidak terkirim saat tidak dicentang, jadi dinormalkan eksplisit.
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar pengguna beserta form tambah/ubah (modal).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = (string) $request->query('role', '');
        $opdId = (string) $request->query('opd', '');

        $query = User::query()->with('opd');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if (array_key_exists($role, User::roles())) {
            $query->where('role', $role);
        }

        if ($opdId !== '' && is_numeric($opdId)) {
            $query->where('opd_id', (int) $opdId);
        }

        return view('users.index', [
            'users' => $query->orderBy('name')->paginate(10)->withQueryString(),
            'roles' => User::roles(),
            'opds' => Opd::where('is_active', true)->orderBy('name')->get(),
            'search' => $search,
            'role' => $role,
            'opd' => $opdId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna "' . $user->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        // Sandi hanya diperbarui bila diisi.
        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna "' . $user->name . '" berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna "' . $name . '" berhasil dihapus.');
    }

    /**
     * Reset sandi pengguna oleh Admin.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi sandi baru tidak cocok.',
            'password.min' => 'Sandi baru minimal 8 karakter.',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Sandi pengguna "' . $user->name . '" berhasil direset.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', Password::min(8)],
            'role' => ['required', Rule::in(array_keys(User::roles()))],
            'opd_id' => ['nullable', Rule::exists('opds', 'id')],
            'nip' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai pengguna lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Sandi wajib diisi.',
            'password.min' => 'Sandi minimal 8 karakter.',
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran tidak dikenal.',
        ]);

        // Unit layanan wajib bagi peran OPD, dikosongkan bagi peran lain.
        $data['opd_id'] = $data['role'] === 'opd' ? ($data['opd_id'] ?? null) : null;

        if ($data['role'] === 'opd' && blank($data['opd_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'opd_id' => 'Unit layanan wajib dipilih untuk pengguna dengan peran OPD.',
            ]);
        }

        return $data;
    }
}

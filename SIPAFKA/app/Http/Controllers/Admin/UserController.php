<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    private const DAFTAR_ROLE = [
        'pelapor' => 'Pelapor',
        'admin' => 'Admin',
        'teknisi' => 'Teknisi',
    ];

    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));

        $query = User::query();

        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', '%' . $cari . '%')
                    ->orWhere('email', 'like', '%' . $cari . '%');
            });
        }

        $users = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $daftarRole = self::DAFTAR_ROLE;

        return view('admin.users.index', compact('users', 'cari', 'daftarRole'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'aktif' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('sukses', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $daftarRole = self::DAFTAR_ROLE;

        return view('admin.users.edit', compact('user', 'daftarRole'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $aktif = $request->has('aktif') ? $request->boolean('aktif') : $user->aktif;

        if ((int) $user->id === (int) Auth::id() && ! $aktif) {
            return back()
                ->withInput()
                ->withErrors(['aktif' => 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.']);
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'aktif' => $aktif,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('sukses', 'Data pengguna berhasil diperbarui.');
    }
}

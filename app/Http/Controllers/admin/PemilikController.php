<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PemilikController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $pemilik = User::where('role', 'pemilik')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('admin.pemilik.index', compact('pemilik'));
    }

    public function show($id)
    {
        $pemilik = User::with('kos')->findOrFail($id);

        return view('admin.pemilik.show', compact('pemilik'));
    }

    public function edit($id)
    {
        $pemilik = User::findOrFail($id);
        return view('admin.pemilik.edit', compact('pemilik'));
    }

    public function update(Request $request, $id)
    {
        $pemilik = User::findOrFail($id);

        // Validasi umum
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'status' => $request->status,
        ];

        // Hanya pemilik sendiri yang bisa ubah email & no_hp
        if (auth()->user()->role === 'pemilik') {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $pemilik->id,
                'no_hp' => 'required|string|max:20',
            ]);

            $data['email'] = $request->email;
            $data['no_hp'] = $request->no_hp;
        }

        $pemilik->update($data);

        if ($request->status === 'nonaktif') {
            $pemilik->kos()->update([
                'status' => 'nonaktif',
                'alasan' => 'Dinonaktifkan karena alamat kos tidak sesuai wilayah Lobener.',
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $pemilik = User::findOrFail($id);
        $pemilik->delete();

        return redirect()->route('admin.pemilik.index')
            ->with('success', 'Data berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    /**
     * Halaman utama siswa: jam, tombol absen, riwayat pribadi.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $today = Carbon::today();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->get();

        $hasMasuk = $absensiHariIni->contains('type', 'Masuk');
        $hasPulang = $absensiHariIni->contains('type', 'Pulang');

        $nextType = ! $hasMasuk ? 'Masuk' : (! $hasPulang ? 'Pulang' : null);

        $riwayat = Absensi::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->limit(30)
            ->get();

        return view('siswa.dashboard', [
            'nextType' => $nextType,
            'riwayat' => $riwayat,
        ]);
    }

    /**
     * Simpan absen masuk/pulang, mirip alur asli (localStorage) tapi ke database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'foto' => ['nullable', 'string'],
            'foto_file' => ['nullable', 'image', 'max:10240'],
        ]);

        $user = $request->user();
        $now = Carbon::now();
        $today = $now->toDateString();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->get();

        $hasMasuk = $absensiHariIni->contains('type', 'Masuk');
        $hasPulang = $absensiHariIni->contains('type', 'Pulang');

        if ($hasMasuk && $hasPulang) {
            return back()->with('error', 'Anda sudah menyelesaikan absensi hari ini!');
        }

        $type = $hasMasuk ? 'Pulang' : 'Masuk';

        if ($type === 'Pulang') {
            $masuk = $absensiHariIni->firstWhere('type', 'Masuk');
            if ($masuk && Carbon::parse($masuk->created_at)->addMinute()->isFuture()) {
                return back()->with('error', 'Harap tunggu minimal 1 menit setelah absen masuk.');
            }
        }

        $fotoPath = null;
        if ($request->hasFile('foto_file')) {
            $fotoPath = $request->file('foto_file')->store('selfie', 'public');
        } elseif (! empty($data['foto'])) {
            $fotoData = $data['foto'];
            if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $typeMatch)) {
                $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
                $extension = strtolower($typeMatch[1]);
                if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $extension = 'jpg';
                }
                $decoded = base64_decode($fotoData);
                if ($decoded !== false) {
                    $fileName = 'selfie/' . uniqid('selfie_', true) . '.' . $extension;
                    Storage::disk('public')->put($fileName, $decoded);
                    $fotoPath = $fileName;
                }
            }
        }

        Absensi::create([
            'user_id' => $user->id,
            'type' => $type,
            'tanggal' => $today,
            'jam' => $now->format('H:i:s'),
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'foto' => $fotoPath,
        ]);

        return back()->with('success', "Absen {$type} berhasil dicatat!");
    }
}

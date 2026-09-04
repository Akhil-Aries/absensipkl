<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Rekap absensi seluruh siswa, dengan filter tanggal & nama.
     */
    public function index(Request $request)
    {
        $query = Absensi::with('user')
            ->orderByDesc('tanggal')
            ->orderByDesc('jam');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->input('nama').'%');
            });
        }

        $absensis = $query->paginate(20)->withQueryString();

        // --- Statistik Dashboard ---
        $today = Carbon::today()->toDateString();

        $totalSiswa = User::where('role', 'siswa')->count();
        $absenMasukHariIni = Absensi::where('tanggal', $today)->where('type', 'Masuk')->distinct('user_id')->count('user_id');
        $absenPulangHariIni = Absensi::where('tanggal', $today)->where('type', 'Pulang')->distinct('user_id')->count('user_id');
        $totalLogHariIni = Absensi::where('tanggal', $today)->count();

        return view('admin.dashboard', [
            'absensis' => $absensis,
            'filterTanggal' => $request->input('tanggal'),
            'filterNama' => $request->input('nama'),
            'totalSiswa' => $totalSiswa,
            'absenMasukHariIni' => $absenMasukHariIni,
            'absenPulangHariIni' => $absenPulangHariIni,
            'totalLogHariIni' => $totalLogHariIni,
        ]);
    }

    /**
     * Export rekap absensi ke CSV (mengikuti filter aktif).
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Absensi::with('user')
            ->orderByDesc('tanggal')
            ->orderByDesc('jam');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->input('nama').'%');
            });
        }

        $records = $query->get();

        $filename = 'rekap-absensi-'.Carbon::now()->format('Y-m-d_H-i-s').'.csv';

        return new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($handle, ['Nama', 'Tanggal', 'Jam', 'Jenis', 'Latitude', 'Longitude', 'Foto']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->user->name,
                    Carbon::parse($record->tanggal)->format('d/m/Y'),
                    Carbon::parse($record->jam)->format('H:i'),
                    $record->type,
                    $record->latitude ?? '-',
                    $record->longitude ?? '-',
                    $record->foto ? url('storage/'.$record->foto) : '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}

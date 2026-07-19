<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Regulasi;
use Illuminate\Http\Request;

class RegulasiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $tahun = trim((string) $request->query('tahun', ''));
        $sort = $request->string('sort', 'latest')->toString();
        $query = Regulasi::query();

        if ($search !== '') {
            $query->where(function ($regulationQuery) use ($search): void {
                $regulationQuery->where('nomor', 'like', '%'.$search.'%')
                    ->orWhere('tentang', 'like', '%'.$search.'%');
            });
        }

        if ($tahun !== '') {
            $query->where('tahun', $tahun);
        }

        $query->when($sort === 'oldest', fn ($regulationQuery) => $regulationQuery->orderBy('tahun')->oldest())
            ->when($sort !== 'oldest', fn ($regulationQuery) => $regulationQuery->orderByDesc('tahun')->latest());

        $regulasis = $query->paginate(12)->withQueryString();
        $daftarTahun = Regulasi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('regulasi.index', [
            'regulasis' => $regulasis,
            'daftarTahun' => $daftarTahun,
            'filters' => compact('search', 'tahun', 'sort'),
            'totalRegulasi' => Regulasi::count(),
        ]);
    }
}

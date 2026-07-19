<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Category;
use App\Models\Regulasi;
use App\Models\Topic;
use Illuminate\Support\Facades\Schema;

class BerandaController extends Controller
{
    public function index()
    {
        $jumlahRegulasi = Schema::hasTable('regulasis') ? Regulasi::count() : 0;
        $jumlahArtikel = Schema::hasTable('artikels') ? Artikel::published()->count() : 0;

        $artikelTerbaru = Schema::hasTable('artikels')
            ? Artikel::published()->with(['category', 'tags', 'user'])->latest('published_at')->take(4)->get()
            : collect();

        $kategoriPopuler = Schema::hasTable('categories')
            ? Category::query()
                ->withCount(['artikels as published_articles_count' => fn ($query) => $query->published()])
                ->orderByDesc('published_articles_count')
                ->take(5)
                ->get()
                ->filter(fn ($category) => $category->published_articles_count > 0)
            : collect();

        $topikTerbaru = Schema::hasTable('topics')
            ? Topic::query()->active()->with('user')->withCount(['replies', 'likes'])->latest()->take(4)->get()
            : collect();

        $jumlahTopik = Schema::hasTable('topics') ? Topic::active()->count() : 0;

        return view('beranda', compact(
            'jumlahRegulasi',
            'jumlahArtikel',
            'jumlahTopik',
            'artikelTerbaru',
            'kategoriPopuler',
            'topikTerbaru',
        ));
    }

    public function knowledge(): \Illuminate\View\View
    {
        return view('knowledge.index', [
            'knowledgeGraphApiUrl' => config('services.knowledge_graph.api_url'),
        ]);
    }

    public function publicDashboard(): \Illuminate\View\View
    {
        return view('public-dashboard.index');
    }
}

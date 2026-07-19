<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Setting::class);

        $settingsAvailable = Schema::hasTable('settings');
        $settings = $settingsAvailable
            ? Setting::query()->with('updater')->whereIn('key', array_keys(Setting::definitions()))->get()->keyBy('key')
            : collect();

        return view('admin.settings.index', [
            'definitions' => Setting::definitions(),
            'settings' => $settings,
            'settingsAvailable' => $settingsAvailable,
            'groups' => [
                [
                    'title' => 'Informasi Publik',
                    'description' => 'Kontak dan label bantuan yang tampil kepada pengunjung portal.',
                    'keys' => [
                        Setting::PUBLIC_CONTACT_EMAIL,
                        Setting::PUBLIC_SUPPORT_LABEL,
                    ],
                ],
                [
                    'title' => 'Halaman Dashboard dan Basis Pengetahuan',
                    'description' => 'Atur sumber tampilan Dashboard serta informasi layanan Basis Pengetahuan.',
                    'keys' => [
                        Setting::DASHBOARD_EMBED_URL,
                        Setting::KNOWLEDGE_BASE_STATUS,
                    ],
                ],
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request, SettingService $settingService): RedirectResponse
    {
        $this->authorize('viewAny', Setting::class);

        $settingService->updateSettings($request->user(), $request->validated('settings'));

        return redirect()->route('admin.settings.index')->with('status', 'Pengaturan berhasil diperbarui.');
    }
}

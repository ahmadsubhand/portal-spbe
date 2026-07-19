<?php

namespace App\Providers;

use App\Models\Artikel;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\ProfileDocument;
use App\Models\Reply;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Models\Topic;
use App\Policies\ArtikelPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ProfileDocumentPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\SettingPolicy;
use App\Policies\TagPolicy;
use App\Policies\TopicPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Artikel::class, ArtikelPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ProfileDocument::class, ProfileDocumentPolicy::class);
        Gate::policy(Reply::class, ReplyPolicy::class);
        Gate::policy(Topic::class, TopicPolicy::class);

        View::composer('*', function ($view): void {
            $settings = [];

            if (Schema::hasTable('settings')) {
                $settings = Setting::query()
                    ->whereIn('key', array_keys(Setting::definitions()))
                    ->pluck('value', 'key')
                    ->all();
            }

            $view->with('portalSettings', $settings);
        });
    }
}

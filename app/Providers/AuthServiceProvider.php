<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Kullanıcı yönetimi yetkisi
        Gate::define('manage-users', function ($user) {
            return $user->hasPermission('user_management');
        });

        // Ürün yönetimi yetkisi
        Gate::define('manage-products', function ($user) {
            return $user->hasPermission('product_management');
        });

        // Kategori yönetimi yetkisi
        Gate::define('manage-categories', function ($user) {
            return $user->hasPermission('category_management');
        });

        // Tedarikçi yönetimi yetkisi
        Gate::define('manage-suppliers', function ($user) {
            return $user->hasPermission('supplier_management');
        });

        // Stok girişi yetkisi
        Gate::define('stock-entry', function ($user) {
            return $user->hasPermission('stock_entry');
        });

        // Stok çıkışı yetkisi
        Gate::define('stock-exit', function ($user) {
            return $user->hasPermission('stock_exit');
        });

        // Stok iadesi yetkisi
        Gate::define('stock-return', function ($user) {
            return $user->hasPermission('stock_return');
        });

        // Stok sayımı yetkisi
        Gate::define('stock-count', function ($user) {
            return $user->hasPermission('stock_count');
        });

        // Raporları görüntüleme yetkisi
        Gate::define('view-reports', function ($user) {
            return $user->hasPermission('reports_view');
        });

        // Rapor export yetkisi
        Gate::define('export-reports', function ($user) {
            return $user->hasPermission('reports_export');
        });

        // Sistem ayarları yetkisi
        Gate::define('manage-settings', function ($user) {
            return $user->hasPermission('system_settings');
        });

        // Admin kontrolü
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Aktif kullanıcı kontrolü
        Gate::define('active-user', function ($user) {
            return $user->isActive();
        });
    }
}

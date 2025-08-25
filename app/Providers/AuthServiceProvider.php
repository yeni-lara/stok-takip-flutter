<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Added this import for User model

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
        // Kullanıcı yönetimi
        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('user_management');
        });

        // Rol yönetimi
        Gate::define('manage-roles', function (User $user) {
            return $user->hasPermission('role_management');
        });

        // Ürün yönetimi
        Gate::define('manage-products', function (User $user) {
            return $user->hasPermission('product_management');
        });

        // Kategori yönetimi
        Gate::define('manage-categories', function (User $user) {
            return $user->hasPermission('category_management');
        });

        // Tedarikçi yönetimi
        Gate::define('manage-suppliers', function (User $user) {
            return $user->hasPermission('supplier_management');
        });

        // Müşteri yönetimi
        Gate::define('manage-customers', function (User $user) {
            return $user->hasPermission('customer_management');
        });

        // Stok işlemleri
        Gate::define('stock_entry', function (User $user) {
            return $user->hasPermission('stock_entry');
        });

        Gate::define('stock_exit', function (User $user) {
            return $user->hasPermission('stock_exit');
        });

        Gate::define('stock_return', function (User $user) {
            return $user->hasPermission('stock_return');
        });

        Gate::define('stock-count', function (User $user) {
            return $user->hasPermission('stock_count');
        });

        // Stok hareket yönetimi
        Gate::define('manage-stock-movements', function (User $user) {
            return $user->hasPermission('stock_movement_management');
        });

        // Rapor görüntüleme
        Gate::define('view-reports', function (User $user) {
            return $user->hasPermission('view_reports');
        });

        // Rapor dışa aktarma
        Gate::define('export-reports', function (User $user) {
            return $user->hasPermission('export_reports');
        });

        // Sistem ayarları
        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermission('settings_management');
        });

        // Admin yetkisi
        Gate::define('admin', function (User $user) {
            return $user->hasPermission('admin');
        });

        // Aktif kullanıcı
        Gate::define('active-user', function (User $user) {
            return $user->isActive();
        });
    }
}

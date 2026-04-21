<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'outlet_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('tenant_id')->constrained('outlets')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('produks', 'tenant_id')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('m_kategoris', 'tenant_id')) {
            Schema::table('m_kategoris', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('bank_accounts', 'tenant_id')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('transaksis', 'tenant_id')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('transaksis', 'outlet_id')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('tenant_id')->constrained('outlets')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('transaksis', 'metode_pembayaran')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->string('metode_pembayaran')->nullable()->after('kembalian');
            });
        }

        if (! Schema::hasColumn('detail_transaksis', 'tenant_id')) {
            Schema::table('detail_transaksis', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','super_admin','owner','admin_toko','kasir') NOT NULL DEFAULT 'kasir'");
        }

        $now = now();
        $tenant = DB::table('tenants')->where('business_code', 'default-business')->first();

        if (! $tenant) {
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'Default Business',
                'business_code' => 'default-business',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $tenantId = $tenant->id;
        }

        $outlet = DB::table('outlets')
            ->where('tenant_id', $tenantId)
            ->where('code', 'OUTLET01')
            ->first();

        if (! $outlet) {
            $outletId = DB::table('outlets')->insertGetId([
                'tenant_id' => $tenantId,
                'name' => 'Outlet Utama',
                'code' => 'OUTLET01',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $outletId = $outlet->id;
        }

        $plan = DB::table('plans')->where('code', 'starter-trial')->first();

        if (! $plan) {
            $planId = DB::table('plans')->insertGetId([
                'name' => 'Starter Trial',
                'code' => 'starter-trial',
                'price' => 0,
                'max_outlets' => 3,
                'max_users' => 10,
                'max_products' => 1000,
                'features' => json_encode([
                    'multi_outlet' => true,
                    'billing_access_owner_only' => true,
                    'mobile_access' => true,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $planId = $plan->id;
        }

        $subscriptionExists = DB::table('subscriptions')
            ->where('tenant_id', $tenantId)
            ->where('plan_id', $planId)
            ->exists();

        if (! $subscriptionExists) {
            DB::table('subscriptions')->insert([
                'tenant_id' => $tenantId,
                'plan_id' => $planId,
                'status' => 'active',
                'starts_at' => $now,
                'trial_ends_at' => $now->copy()->addDays(30),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'updated_at' => $now,
        ]);

        DB::table('produks')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'updated_at' => $now,
        ]);

        DB::table('m_kategoris')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'updated_at' => $now,
        ]);

        DB::table('bank_accounts')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'updated_at' => $now,
        ]);

        DB::table('transaksis')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'outlet_id' => $outletId,
            'updated_at' => $now,
        ]);

        DB::table('detail_transaksis')->whereNull('tenant_id')->update([
            'tenant_id' => $tenantId,
            'updated_at' => $now,
        ]);

        $adminUsers = DB::table('users')->where('role', 'admin')->orderBy('id')->get(['id']);
        $firstAdminId = $adminUsers->first()->id ?? null;

        if ($firstAdminId) {
            DB::table('users')->where('id', $firstAdminId)->update(['role' => 'owner']);
            DB::table('users')
                ->where('role', 'admin')
                ->where('id', '!=', $firstAdminId)
                ->update(['role' => 'admin_toko']);
        }

        DB::table('users')
            ->where('role', 'kasir')
            ->whereNull('outlet_id')
            ->update(['outlet_id' => $outletId]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','owner','admin_toko','kasir') NOT NULL DEFAULT 'kasir'");
        }

        $products = DB::table('produks')->get(['id', 'tenant_id', 'stok']);

        foreach ($products as $product) {
            $productTenantId = $product->tenant_id ?: $tenantId;

            $mappingExists = DB::table('product_outlets')
                ->where('produk_id', $product->id)
                ->where('outlet_id', $outletId)
                ->exists();

            if (! $mappingExists) {
                DB::table('product_outlets')->insert([
                    'tenant_id' => $productTenantId,
                    'produk_id' => $product->id,
                    'outlet_id' => $outletId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $stockExists = DB::table('product_stocks')
                ->where('produk_id', $product->id)
                ->where('outlet_id', $outletId)
                ->exists();

            if (! $stockExists) {
                DB::table('product_stocks')->insert([
                    'tenant_id' => $productTenantId,
                    'produk_id' => $product->id,
                    'outlet_id' => $outletId,
                    'stock' => (int) ($product->stok ?? 0),
                    'minimum_stock' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('outlet_id');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('metode_pembayaran');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('m_kategoris', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('outlet_id');
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};

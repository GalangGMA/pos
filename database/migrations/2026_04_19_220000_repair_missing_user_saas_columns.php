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

        $tenant = DB::table('tenants')->where('business_code', 'default-business')->first();
        $outlet = null;

        if ($tenant) {
            $outlet = DB::table('outlets')
                ->where('tenant_id', $tenant->id)
                ->where('code', 'OUTLET01')
                ->first();

            DB::table('users')
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $tenant->id]);
        }

        if ($outlet) {
            DB::table('users')
                ->where('role', 'kasir')
                ->whereNull('outlet_id')
                ->update(['outlet_id' => $outlet->id]);
        }

        DB::table('users')->where('role', 'admin')->update(['role' => 'admin_toko']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'outlet_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        if (Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }
    }
};

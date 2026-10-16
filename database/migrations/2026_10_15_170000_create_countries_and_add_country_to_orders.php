<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('name', 100);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });

        $now = now();
        $rows = json_decode((string) file_get_contents(database_path('data/countries.json')), true, flags: JSON_THROW_ON_ERROR);
        DB::table('countries')->insert(array_map(fn (array $row) => [
            'code' => $row['code'],
            'name' => $row['name'],
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows));

        Schema::table('orders', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });

        Schema::dropIfExists('countries');
    }
};

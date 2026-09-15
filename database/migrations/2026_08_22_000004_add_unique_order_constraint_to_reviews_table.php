<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateOrder = DB::table('reviews')
            ->select('request_id')
            ->groupBy('request_id')
            ->havingRaw('count(*) > 1')
            ->value('request_id');

        if ($duplicateOrder !== null) {
            throw new RuntimeException(
                'Duplicate reviews for the same Order must be resolved before adding the unique constraint.'
            );
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('request_id', 'reviews_request_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_request_unique');
        });
    }
};

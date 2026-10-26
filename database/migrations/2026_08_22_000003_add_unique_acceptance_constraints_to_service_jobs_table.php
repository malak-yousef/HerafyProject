<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateOrder = DB::table('service_jobs')
            ->select('order_id')
            ->groupBy('order_id')
            ->havingRaw('count(*) > 1')
            ->value('order_id');
        $duplicateOffer = DB::table('service_jobs')
            ->select('offer_id')
            ->groupBy('offer_id')
            ->havingRaw('count(*) > 1')
            ->value('offer_id');

        if ($duplicateOrder !== null || $duplicateOffer !== null) {
            throw new RuntimeException(
                'Duplicate service_jobs must be resolved before adding unique Order and Offer constraints.'
            );
        }

        Schema::table('service_jobs', function (Blueprint $table) {
            $table->unique('order_id', 'service_jobs_order_unique');
            $table->unique('offer_id', 'service_jobs_offer_unique');
        });
    }

    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropUnique('service_jobs_order_unique');
            $table->dropUnique('service_jobs_offer_unique');
        });
    }
};

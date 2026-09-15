<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('profession_id')->nullable()->after('customer_id')->constrained()->cascadeOnDelete();
        });

        if (DB::table('orders')->whereNull('profession_id')->exists()) {
            $fallbackProfessionId = DB::table('professions')->where('status', 'active')->orderBy('id')->value('id')
                ?? DB::table('professions')->orderBy('id')->value('id');

            if ($fallbackProfessionId === null) {
                throw new RuntimeException('Existing orders require at least one profession before profession_id can be backfilled.');
            }

            DB::table('orders')->whereNull('profession_id')->orderBy('id')->eachById(
                function ($orders) use ($fallbackProfessionId): void {
                    foreach ($orders as $order) {
                        $professionId = DB::table('offers')
                            ->join('craftmen', 'craftmen.id', '=', 'offers.craftman_id')
                            ->where('offers.order_id', $order->id)
                            ->orderByRaw("case when offers.status = 'accepted' then 0 else 1 end")
                            ->orderBy('offers.id')
                            ->value('craftmen.profession_id') ?? $fallbackProfessionId;

                        DB::table('orders')->where('id', $order->id)->update(['profession_id' => $professionId]);
                    }
                }
            );
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('profession_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profession_id');
        });
    }
};

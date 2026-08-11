<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('status')->default('active')->after('category');
            $table->decimal('price', 10, 2)->nullable()->after('status');
            $table->integer('views')->default(0)->after('price');
            $table->softDeletes()->after('views');
            $table->json('images')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['category', 'status', 'price', 'views', 'images', 'deleted_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('category')->nullable();
            $table->string('status')->nullable();
            $table->string('duration')->nullable();
            $table->string('role')->nullable();
            $table->string('documentation_url')->nullable();
            $table->string('video_demo_url')->nullable();
            $table->json('overview')->nullable();
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->json('architecture')->nullable();
            $table->json('challenges')->nullable();
            $table->json('timeline')->nullable();
            $table->json('performance')->nullable();
            $table->json('security_details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'category',
                'status',
                'duration',
                'role',
                'documentation_url',
                'video_demo_url',
                'overview',
                'gallery',
                'features',
                'architecture',
                'challenges',
                'timeline',
                'performance',
                'security_details',
            ]);
        });
    }
};

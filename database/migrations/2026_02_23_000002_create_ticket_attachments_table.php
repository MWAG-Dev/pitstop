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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('mime_type', 255)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('storage_disk', 64)->default('local');
            $table->string('storage_path');
            $table->string('source', 40)->default('inbound_email');
            $table->timestamps();

            $table->index(['ticket_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};

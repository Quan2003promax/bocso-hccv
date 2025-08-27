<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailPhoneFileToServiceRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_registrations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('identity_number');
            $table->string('phone')->nullable()->after('email');
            $table->string('document_file')->nullable()->after('phone');
            $table->string('document_original_name')->nullable()->after('document_file');
            $table->string('document_mime_type')->nullable()->after('document_original_name');
            $table->bigInteger('document_size')->nullable()->after('document_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_registrations', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'document_file', 'document_original_name', 'document_mime_type', 'document_size']);
        });
    }
}

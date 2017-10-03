<?php

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeServicesToUseAMoreUniqueIdentifier extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['file']);

            $table->string('author')->change();
            $table->char('uuid', 36)->after('id');
            $table->dropColumn('folder');
        });

        DB::table('services')->get(['id', 'author', 'uuid'])->each(function ($service) {
            DB::table('services')->where('id', $service->id)->update([
               'author' => ($service->author === 'ptrdctyl-v040-11e6-8b77-86f30ca893d3') ? 'support@pterodactyl.io' : 'unknown@unknown-author.com',
               'uuid' => Uuid::uuid4()->toString(),
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->string('folder')->unique('file');
            $table->char('author', 36)->change();

            $table->unique('name');
        });
    }
}

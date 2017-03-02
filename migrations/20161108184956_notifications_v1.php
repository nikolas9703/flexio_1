<?php

use \Flexio\Migration\Migration;

class NotificationsV1 extends Migration
{
   public function up()
   {
       $this->schema->create('notifications', function(Illuminate\Database\Schema\Blueprint $table) {
           $table->uuid('id')->primary();
           $table->string('type');
           $table->morphs('notifiable');
           $table->text('data');
           $table->timestamp('read_at')->nullable();
           $table->timestamps();
       });
   }


   public function down()
   {
      $this->dropTable('notifications');
   }
}

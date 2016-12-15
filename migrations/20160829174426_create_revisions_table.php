<?php

use \Flexio\Migration\Migration;

class CreateRevisionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $exist = $this->hasTable('revisions');
    if(!$exist) {
      $this->schema->create('revisions', function ($table) {
          $table->increments('id');
          $table->string('revisionable_type');
          $table->integer('revisionable_id');
          $table->integer('user_id')->nullable();
          $table->string('key');
          $table->text('old_value')->nullable();
          $table->text('new_value')->nullable();
          $table->timestamps();
          $table->index(array('revisionable_id', 'revisionable_type'));
      });
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      $this->schema->drop('revisions');
  }
}

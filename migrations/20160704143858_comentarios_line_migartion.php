<?php

use \Flexio\Migration\Migration;

class ComentariosLineMigartion extends Migration
{
   public function up() {
        $this->schema->create('lines_items_comentarios', function(Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->text('comentario');
            $table->string('comentable_type',250);
            $table->integer('usuario_id');
            $table->timestamps();
        });
    }
}

<?php

use \Flexio\Migration\Migration;

class DocumentosV1 extends Migration
{
    
    public function up(){
        
        $table = $this->table('doc_documentos')
                ->addColumn('nombre_documento','string',['limit'=>140,'default'=>'']);
        
        if(!$table->hasColumn('documentable_type')){
            
            $table->addColumn('documentable_type','string',['limit'=>140]);
            
        }
        
        if(!$table->hasColumn('documentable_id')){
            
            $table->addColumn('documentable_id','integer',['limit'=>11]);
            
        }
        
        $table->save();
        
    }
    
    public function down(){
        
        $table = $this->table('doc_documentos')
                ->removeColumn('nombre_documento');
        
        if(!$table->hasColumn('documentable_type')){
            
            $table->removeColumn('documentable_type');
            
        }
        
        if(!$table->hasColumn('documentable_id')){
            
            $table->removeColumn('documentable_id');
            
        }
        
        $table->save();
        
    }
    
}

<?php

class Devolucao extends Zend_Db_Table_Row_Abstract {

    
    public function inserirDevolucao($post){
        
        $tDevolucao = new DbTable_Devolucao;
        $novaDevolucao = $tDevolucao->createRow();
        $novaDevolucao->setFromArray($post);
        $novaDevolucao->save();
        
    }
    
    public function atualizarStatusDevolucao($id, $post){
        
               
        $tDevolucao = new DbTable_Devolucao;
        $devolucao = $tDevolucao->find($id);
        
        $devolucao->current()->setFromArray($post);
        $devolucao->current()->save();
        
        
    }
    
    
    
    
}

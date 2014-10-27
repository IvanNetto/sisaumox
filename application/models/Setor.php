<?php

class Setor extends Zend_Db_Table_Row_Abstract {

    public function listarSetores() {

        $tSetor = new DbTable_Setor();
        return $listaDeSetores = $tSetor->fetchAll();
    }

    public function findSetorById($id) {

        $tSetor = new DbTable_Setor();
        return $listaDeSetores = $tSetor->find($id);
    }

    public function inserirSetor($post) {

        $tSetor = new DbTable_Setor();
        $novoSetor = $tSetor->createRow();
        $novoSetor->setFromArray($post);
        $novoSetor->save();
    }

    public function editarSetor($post, $setor) {
        
        $setor->current()->setFromArray($post);    
        $setor->current()->save();
        
        
    }
    
}

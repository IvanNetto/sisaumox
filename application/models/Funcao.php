<?php

class Funcao extends Zend_Db_Table_Row_Abstract {

    public function listarFuncoes() {

        $tFuncao = new DbTable_Funcao();
        return $listaDeFuncoes = $tFuncao->fetchAll();
    }

    public function findFuncaoById($id) {

        $tFuncao = new DbTable_Funcao();
        return $listaDeFuncoes = $tFuncao->find($id);
    }

    public function inserirFuncao($post) {

        $tFuncao = new DbTable_Funcao();
        $novoFuncao = $tFuncao->createRow();
        $novoFuncao->setFromArray($post);
        $novoFuncao->save();
    }

    public function editarFuncao($post, $funcao) {
        
        $funcao->current()->setFromArray($post);    
        $funcao->current()->save();
        
        
    }
    
}

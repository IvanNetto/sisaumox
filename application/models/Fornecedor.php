<?php

class Fornecedor extends Zend_Db_Table_Row_Abstract {

    public function listarFornecedores() {

        $tFornecedor = new DbTable_Fornecedor();
        return $listaDeFornecedores = $tFornecedor->fetchAll();
    }

    public function findFornecedorById($id) {

        $tFornecedor = new DbTable_Fornecedor();
        return $fornecedor = $tFornecedor->find($id);
        
    }

    public function inserirFornecedor($post) {

        $tFornecedor = new DbTable_Fornecedor();
        $novoFornecedor = $tFornecedor->createRow();
        $novoFornecedor->setFromArray($post);
        $novoFornecedor->save();
    }

    public function editarFornecedor($post, $fornecedor) {
        
        $fornecedor->current()->setFromArray($post);    
        $fornecedor->current()->save();
        
        
    }
    
}

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

        $semPonto = explode('.', $post['id']);
        $var = $semPonto[0] . $semPonto[1];

        $semBarra = explode('/', $semPonto[2]);
        $var = $var . $semBarra[0];

        $semTraco = explode('-', $semBarra[1]);
        $var = $var . $semTraco[0] . $semTraco[1];

        $arrayEntra = ['id' => $var];
        $array = array_replace($post, $arrayEntra);

        $tFornecedor = new DbTable_Fornecedor();
        $novoFornecedor = $tFornecedor->createRow();
        $novoFornecedor->setFromArray($array);
        $novoFornecedor->save();
    }

    public function editarFornecedor($post, $fornecedor) {

        
        $semPonto = explode('.', $post['id']);
        $var = $semPonto[0] . $semPonto[1];

        $semBarra = explode('/', $semPonto[2]);
        $var = $var . $semBarra[0];

        $semTraco = explode('-', $semBarra[1]);
        $var = $var . $semTraco[0] . $semTraco[1];
        
        $arrayEntra = ['id' => $var];
        
        $array = array_replace($post, $arrayEntra);
        
        $fornecedor->current()->setFromArray($array);
        $fornecedor->current()->save();
    }

}

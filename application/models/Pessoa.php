<?php

class Pessoa extends Zend_Db_Table_Row_Abstract {

    public function listarPessoas() {

        $tPessoa = new DbTable_Pessoa();
        return $listaDePessoas = $tPessoa->fetchAll();
    }

    public function findPessoaById($id) {

        $tPessoa = new DbTable_Pessoa();
        return $listaDePessoas = $tPessoa->find($id);
      
    }

    public function inserirPessoa($post) {

        $tPessoa = new DbTable_Pessoa();
        $novaPessoa = $tPessoa->createRow();
        $novaPessoa->setFromArray($post);
        $novaPessoa->save();
    }

    public function editarPessoa($post, $pessoa) {

        $tProduto = new DbTable_Pessoa();
        $pessoa->current()->setFromArray($post, $pessoa);
        $pessoa->current()->save();
    }

}

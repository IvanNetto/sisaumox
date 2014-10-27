<?php

class Categoria extends Zend_Db_Table_Row_Abstract {

    public function listarCategorias() {

        $tCategoria = new DbTable_Categoria();
        return $listaDeCategorias = $tCategoria->fetchAll();
    }

    public function findCategoriaById($id) {

        $tCategoria = new DbTable_Categoria();
        return $categoria = $tCategoria->find($id);
    }

    public function inserirCategoria($post) {

        $tCategoria = new DbTable_Categoria();

        $novaCategoria = $tCategoria->createRow();
        $novaCategoria->setFromArray($post);
        $novaCategoria->save();
    }

    public function editarCategoria($post, $categoria) {

        $tCategoria = new DbTable_Categoria();

        $categoria->current()->setFromArray($post);
        $categoria->current()->save();
    }
    
}

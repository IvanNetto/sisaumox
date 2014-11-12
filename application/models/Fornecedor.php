<?php

class Fornecedor extends Zend_Db_Table_Row_Abstract {

    public function listarPerfis() {

        $tPerfil = new DbTable_Perfil();
        return $listaDePerfis = $tPerfil->fetchAll();
    }

    public function findPerfilById($id) {

        $tPerfil = new DbTable_Perfil();
        return $listaDePerfis = $tPerfil->find($id);
        
    }

    public function inserirPerfil($post) {

        $tPerfil = new DbTable_Perfil();
        $novoPerfil = $tPerfil->createRow();
        $novoPerfil->setFromArray($post);
        $novoPerfil->save();
    }

    public function editarPerfil($post, $perfil) {
        
        $perfil->current()->setFromArray($post);    
        $perfil->current()->save();
        
        
    }
    
}

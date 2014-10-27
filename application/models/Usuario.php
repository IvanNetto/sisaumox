<?php

class Usuario extends Zend_Db_Table_Row_Abstract {

    public function listarUsuarios() {

        $tUsuario = new DbTable_Usuario();
        return $usuarios = $tUsuario->fetchAll();
    }

    public function findUsuarioById($id) {

        $tUsuario = new DbTable_Usuario();
        return $listaDeUsuarios = $tUsuario->find($id);
    }

    public function inserirUsuario($post) {

        $tUsuario = new DbTable_Usuario();
        $novoUsuario = $tUsuario->createRow();
        $novoUsuario->setFromArray($post);
        $novoUsuario->save();
    }

    public function editarUsuario($post, $usuario) {

        $usuario->current()->setFromArray($post);
        $usuario->current()->save();
    }

}

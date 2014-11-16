<?php

class Compra extends Zend_Db_Table_Row_Abstract {

    public function listarComprasAtivas($status) {
        
        $tCompra = new DbTable_Compra();
        $query = $tCompra->select()
                ->where('status in (?)', $status);

        return $tCompra->fetchAll($query);
    }

    public function inserir($post) {

            $tCompra = new DbTable_Compra();
            $novaCompra = $tCompra->createRow();
            $novaCompra->setFromArray($post);
            $novaCompra->save();
    }

    public function atualizarCompra($objCompra, $post){
        
            $novaCompra = $objCompra->setFromArray($post);
            $novaCompra = $objCompra->save();
       
        
    }
        
}

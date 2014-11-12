<?php
class DbTable_Compra extends Zend_Db_Table_Abstract
{

	protected $_name = 't_compra';
	protected $_rowClass = 'Compra';
        protected $_primary = 'id';
        
        
        public function gerarPedidoDeCompra($compraId){
            
            $sql = "select * from t_compra c, t_produto_compra pc where c.id = pc.compraid and pc.compraid = $compraId";
            
            return $this->getAdapter()->fetchAll($sql);
            
        }

}
<?php

class DbTable_Produtocompra extends Zend_Db_Table_Abstract {

    protected $_name = 't_produto_compra';
    protected $_rowClass = 'Produtocompra';
    protected $_primary = 'id';

    public function verificarSeJahExisteItemEmCompraAtiva() {

        $sql = "select pc.produtoid from t_produto_compra pc, t_compra c
                where pc.compraid = c.id                
                and c.status in ('nova', 'em andamento','pendente', 'aguardando fornecedor' ,'aprovada')";

        return $this->getAdapter()->fetchAll($sql);
    }

}

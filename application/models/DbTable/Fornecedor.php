<?php

class DbTable_Fornecedor extends Zend_Db_Table_Abstract {

    protected $_name = 't_fornecedor';
    protected $_rowClass = 'Fornecedor';
    protected $_primary = 'id';

    public function listarFornecedoresPorProduto() {


        $sql = "select fornecedor.razao_social, produto.nome, compra.id, compra.data_pedido
                from t_fornecedor fornecedor, t_produto produto, t_compra compra, t_produto_compra pc
                where pc.produtoid = produto.id
                and   pc.compraid = compra.id
                and   fornecedor.id = compra.fornecedorid
                order by fornecedor.razao_social";

        return $this->getAdapter()->fetchAll($sql);
    }

}

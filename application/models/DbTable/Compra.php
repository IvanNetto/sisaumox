<?php

class DbTable_Compra extends Zend_Db_Table_Abstract {

    protected $_name = 't_compra';
    protected $_rowClass = 'Compra';
    protected $_primary = 'id';

    public function gerarPedidoDeCompra($compraId) {

        $sql = "select * from t_compra c, t_produto_compra pc where c.id = pc.compraid and pc.compraid = $compraId";

        return $this->getAdapter()->fetchAll($sql);
    }

    public function comprasPorPeriodo($data1, $data2) {

        $data1 = explode('-', $data1);
        $ano = $data1[0];
        $mes = $data1[1];
        $dia = $data1[2];

        $data1 = $data1[2] . '/' . $data1[1] . '/' . $data1[0];

        $data2 = explode('-', $data2);
        $ano = $data2[0];
        $mes = $data2[1];
        $dia = $data2[2];

        $data2 = $data2[2] . '/' . $data2[1] . '/' . $data2[0];

        $sql = "select * from t_compra where data_pedido between '$data1' and '$data2' order by status"; 

        return $this->getAdapter()->fetchAll($sql);
    }

}

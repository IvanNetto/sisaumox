<?php

class DbTable_Produto extends Zend_Db_Table_Abstract {

    protected $_name = 't_produto';
    protected $_rowClass = 'Produto';
    protected $_primary = 'id';

    public function listarIndisponiveis() {

        $sql = "select * from t_produto where quantidade = '0'";


        return $this->getAdapter()->fetchAll($sql);
    }
    
      public function listarQuantidadeMinima() {

        $sql = "select * from t_produto where quantidademinima >= quantidade";


        return $this->getAdapter()->fetchAll($sql);
    }

    public function produtosMaisSolicitadosPorPeriodo($data1, $data2) {

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

        $sql = "SELECT produto.nome, SUM(ps.quantidade) AS total FROM t_produto_solicitacao ps, t_produto produto, t_solicitacao sol 
                where produto.id = ps.produtoid and ps.solicitacaoid = sol.id and sol.data between '$data1' and '$data2' 
                GROUP BY produto.nome ORDER BY total DESC";

        return $this->getAdapter()->fetchAll($sql);
    }

}

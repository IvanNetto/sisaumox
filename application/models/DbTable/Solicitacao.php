<?php

class DbTable_Solicitacao extends Zend_Db_Table_Abstract {

    protected $_name = 't_solicitacao';
    protected $_rowClass = 'Solicitacao';
    protected $_primary = 'id';

    public function mostrarUltimaSolicitacaoAgendada() {

        $sql = "select max(id) as id from t_solicitacao where status ='agendada'";

        return $this->getAdapter()->fetchAll($sql);
    }

    public function RelatorioDeSolicitacoesPorPeriodo($data1, $data2, $status) {
        
        $status = strtolower($status);

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
        
        if ($status){
        $sql = "select distinct * from t_solicitacao s, t_usuario u where u.id=s.usuarioid and status = '$status' and data between '$data1' and '$data2' order by data";
        }else{
        $sql = "select distinct * from t_solicitacao s, t_usuario u where u.id=s.usuarioid and data between '$data1' and '$data2' order by data";       
            
        }
    
        return $this->getAdapter()->fetchAll($sql);
    }
    
    public function relatorioDeSolicitacoesPorUsuario ($usuario){
        
        $sql = "select * from t_solicitacao where usuarioid = $usuario";

        return $this->getAdapter()->fetchAll($sql);
        
        
        
    }

}

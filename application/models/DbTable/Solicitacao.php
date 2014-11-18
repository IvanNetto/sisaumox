<?php
class DbTable_Solicitacao extends Zend_Db_Table_Abstract
{

    protected $_name = 't_solicitacao';
    protected $_rowClass = 'Solicitacao';
    protected $_primary = 'id';



   public function mostrarUltimaSolicitacaoAgendada(){

       $sql = "select max(id) as id from t_solicitacao where status ='agendada'";

       return $this->getAdapter()->fetchAll($sql);


   }

}
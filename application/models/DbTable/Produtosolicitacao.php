<?php
class DbTable_Produtosolicitacao extends Zend_Db_Table_Abstract
{

    protected $_name = 't_produto_solicitacao';
    protected $_rowClass = 'Produtosolicitacao';
    protected $_primary = 'id';

    
    
     public function verificarSeJahExisteItemEmSolicitacaoAtivaDoUsuario($usuarioid){
        
        $sql = "select ps.produtoid from t_produto_solicitacao ps, t_solicitacao s
                where ps.solicitacaoid = s.id                
                and s.usuarioid = $usuarioid
                and s.status in ('nova','rejeitada','em analise','agendada')"; //echo $sql;die;

        return $this->getAdapter()->fetchAll($sql);
       
        
    }
    
    
}
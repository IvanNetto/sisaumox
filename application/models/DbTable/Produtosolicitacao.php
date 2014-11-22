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
                and s.status in ('nova','rejeitada','em analise','agendada', 'entregue','aprovada')"; 

        return $this->getAdapter()->fetchAll($sql);
       
        
    }
    
    public function quantidadeJahAprovadaParcialPorProdutoESolicitacao($produtoId, $solicitacaoId){
        
         $sql = "select aprovacao_parcial from t_produto_solicitacao
                where produtoid = $produtoId
                and solicitacaoid = $solicitacaoId"; 

        return $this->getAdapter()->fetchAll($sql);
        
    }
    
    
}
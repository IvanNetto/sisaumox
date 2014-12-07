<?php
class DbTable_Devolucao extends Zend_Db_Table_Abstract
{

    protected $_name = 't_devolucao';
    protected $_rowClass = 'Devolucao';
    protected $_primary = 'id_devolucao';
    
    
    
      public function findDevolucaoByProduto($produtoid){
          
        $sql = "select * from t_produto_solicitacao ps, t_devolucao dev where ps.id = dev.produtosolicitacaoid and ps.produtoid = $produtoid"; 

        return $this->getAdapter()->fetchAll($sql);
       
        
    }
    
    public function findDevolucaoByProdutoSolicitacao($produtosolicitacaoid){
        
        $sql = "select * from t_devolucao dev where dev.produtosolicitacaoid = $produtosolicitacaoid and status_devolucao = 'NOVA'";

        
        return $this->getAdapter()->fetchAll($sql);
    }
    
    
    public function somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtoSolicitacaoId){
        
           $sql = "select sum(dev.quantidade_devolvida) as quantidade from t_produto_solicitacao ps, t_devolucao dev where ps.id = dev.produtosolicitacaoid and ps.id = $produtoSolicitacaoId"; 

        return $this->getAdapter()->fetchAll($sql);
        
    }
    
    public function listarNovasDevolucoes(){
        
        $sql = "select * from t_devolucao dev, t_solicitacao sol, t_produto_solicitacao ps 
                where sol.id = ps.solicitacaoid 
                and ps.id = dev.produtosolicitacaoid 
                and dev.status_devolucao in ('nova','aprovada')";
                
        return $this->getAdapter()->fetchAll($sql);
        
    }
    
    public function findProdutoByDevolucao($id){
                
        $sql = "select pr.id from t_produto pr, t_devolucao dev, t_produto_solicitacao ps 
                where dev.produtosolicitacaoid = ps.id 
                and pr.id = ps.produtoid
                and dev.id_devolucao = $id";
              
        return $this->getAdapter()->fetchAll($sql);
    }
    
    public function listarDevolucoesAntigas($produtosolicitacao){
        
        $sql = "select * from t_solicitacao s, t_devolucao d, t_produto_solicitacao ps 
                where s.id = ps.solicitacaoid
                and ps.id = d.produtosolicitacaoid
                and s.usuarioid = '6' 
                and s.status = 'recebida' 
                and d.status_devolucao in ('entregue', 'reprovada')";

        
        return $this->getAdapter()->fetchAll($sql);
    }
    
    

}
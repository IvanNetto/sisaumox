<?php

/**
 * Created by PhpStorm.
 * User: nettoinf
 * Date: 20/10/14
 * Time: 10:12
 */
class Produtosolicitacao extends Zend_Db_Table_Row_Abstract {

    /**
     * Insere no carrinho de solicitações
     */
    public function inserirProdutoSolicitacao($solicitacao,$produtosescolhidos) {
        
        
        for ($i = 0; $i < count($produtosescolhidos); $i++) 
         {
            $post = array('produtoid' => $produtosescolhidos[$i], 'solicitacaoid' => $solicitacao);
                
            $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
            $novaProdutoSolicitacao = $tProdutoSolicitacao->createRow();
            $novaProdutoSolicitacao->setFromArray($post);
            $novaProdutoSolicitacao->save();
        }

        //depois inclui cada linha de cada produto
    }

    public function exibirCarrinhoDeSolicitacoes($solicitacaoid) {
 
         $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
         $query = $tProdutoSolicitacao->select()
            ->where('solicitacaoid = (?)', $solicitacaoid);

        return  $tProdutoSolicitacao->fetchAll($query);       
   
        
    }
    
    public function resumoDeSolicitacao($solicitacaoid) {
        
          $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
          return $tProdutoSolicitacao->find($solicitacaoid);
        
    }
    
    public function cancelarCarrinhoDeCompras($solicitacaoid){
        
        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        
        $query = $tProdutoSolicitacao->select()
            ->where('solicitacaoid = (?)', $solicitacaoid);
        
        $tProdutoSolicitacao->delete($query);

        
        
    }

}

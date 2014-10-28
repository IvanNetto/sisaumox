<?php

class Produtosolicitacao extends Zend_Db_Table_Row_Abstract {

    
    public function findByProdutoESolicitacao($solicitacaoid, $produtoid){
        
        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutoSolicitacao->select()
                ->where('solicitacaoid = (?)', $solicitacaoid)
                ->where('produtoid = (?)', $produtoid);

        return $tProdutoSolicitacao->fetchAll($query);
                
    }
    
    
    public function inserirProdutoSolicitacao($solicitacaoid, $produtosescolhidos) {

        
        
        for ($i = 0; $i < count($produtosescolhidos); $i++) {
            
            $jahExisteOItemNoCarrinho = $this->findByProdutoESolicitacao($solicitacaoid, $produtosescolhidos[$i])->current();
            
            if($jahExisteOItemNoCarrinho <> null){
                
                throw new exception("Você já moveu este item para o carrinho de solicitações!");
                return $produtosescolhidos[$i];
                
            }else{
            
            $post = array('produtoid' => $produtosescolhidos[$i], 'solicitacaoid' => $solicitacaoid);

            $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
            $novaProdutoSolicitacao = $tProdutoSolicitacao->createRow();
            $novaProdutoSolicitacao->setFromArray($post);
            $novaProdutoSolicitacao->save();
            
            }
        }

    }

    public function exibirCarrinhoDeSolicitacoes($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutoSolicitacao->select()
                ->where('solicitacaoid = (?)', $solicitacaoid);

        return $tProdutoSolicitacao->fetchAll($query);
    }

    public function resumoDeSolicitacao($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutoSolicitacao->select()
                ->where('solicitacaoid = (?)', $solicitacaoid);
        
        return $tProdutoSolicitacao->fetchAll($query);
    }

    public function cancelarCarrinhoDeCompras($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();

        $query = $tProdutoSolicitacao->select()
                ->where('solicitacaoid = (?)', $solicitacaoid);
       //vai deletar ??? não deve criar um foreach e deletar linha por linha?
        $tProdutoSolicitacao->delete($query);
    }

//    public function deletarItemDoCarrinhoDeSolicitacao($solicitacaoid, $produtoid) {
//
//        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
//
//        $query = $tProdutoSolicitacao->select()
//                ->where('solicitacaoid = (?)', $solicitacaoid)
//                ->where('produtoid = (?)', $produtoid);
//      
//        $tProdutoSolicitacao->delete($query);
//        
//    }

}

<?php

class Produtocompra extends Zend_Db_Table_Row_Abstract {

    public function findByProdutoECompra($compraId, $produtoId) {

        $tProdutoCompra = new DbTable_Produtocompra();
        $query = $tProdutoCompra->select()
                ->where('compraid = (?)', $compraId)
                ->where('produtoid = (?)', $produtoId);

        return $tProdutoCompra->fetchAll($query);
    }

    public function findBySolicitacao($solicitacaoid){


        $tProdutosolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutosolicitacao->select()
            ->where('solicitacaoid = (?)', $solicitacaoid);

        return $tProdutosolicitacao->fetchAll($query);


    }

    public function listarProdutosPermitidos($categoriaid, $listaItensProibidos) {

        if ($listaItensProibidos) {
            $tProduto = new DbTable_Produto();
            $query = $tProduto->select()
                    ->where('categoriaid = (?)', $categoriaid)
                    ->where('id NOT IN (?)', $listaItensProibidos);

            return $tProduto->fetchAll($query);
        } else {

            $tProduto = new Produto();
            return $tProduto->findProdutoByCategoriaid($categoriaid);
        }
    }

    public function inserirProdutoCompra($compraId, $produtosEscolhidos) {
        
        for ($i = 0; $i < count($produtosEscolhidos); $i++) {

            $jahExisteOItemNoCarrinho = $this->findByProdutoECompra($compraId, $produtosEscolhidos[$i])->current();

            if ($jahExisteOItemNoCarrinho <> null) {

                throw new exception("Você já moveu este item para o carrinho de solicitações!");
                return $produtosescolhidos[$i];
            } else {

                $post = array('produtoid' => $produtosEscolhidos[$i], 'compraid' => $compraId);
                
                $tProdutoCompra = new DbTable_ProdutoCompra();
                $novoItemNoCarrinho = $tProdutoCompra->createRow();
                $novoItemNoCarrinho->setFromArray($post);
                $novoItemNoCarrinho->save();
            }
        }
    }

    public function exibirCarrinhoDeCompras($compraId) {

        $tProdutoCompra = new DbTable_Produtocompra();
        $query = $tProdutoCompra->select()
                ->where('compraid = (?)', $compraId);

        return $tProdutoCompra->fetchAll($query);
    }

    public function resumoDeSolicitacao($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutoSolicitacao->select()
                ->where('solicitacaoid = (?)', $solicitacaoid);

        return $tProdutoSolicitacao->fetchAll($query);
    }

    public function limparCarrinhoDeSolicitacao($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $where = $tProdutoSolicitacao->getAdapter()->quoteInto('solicitacaoid = (?)', $solicitacaoid);

        $tProdutoSolicitacao->delete($where);
       
    }
    
    public function deletarItemDeQuantidadeMaximaUltrapassada($solicitacaoid, $produto){
        
         $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
         $where[] = $tProdutoSolicitacao->getAdapter()->quoteInto('solicitacaoid = (?)', $solicitacaoid);
         $where[] = $tProdutoSolicitacao->getAdapter()->quoteInto('produtoid = (?)', $produto);
                
         $tProdutoSolicitacao->delete($where);
        
    }

    public function registrarQuantidadeDoProdutoNaSolicitacao($produtos, $quantidade, $solicitacaoid) {


        foreach ($produtos as $produto) {

            $itemSelecionado = $this->findByProdutoESolicitacao($solicitacaoid, $produto)->current();

            $post = ['quantidade' => $quantidade];

            $itemSelecionado->setFromArray($post);
            $itemSelecionado->save();
        }
    }

    public function inserirProdutoNaSolicitacaoAgendada($solicitacaoid, $produtoId, $quantidade, $data_agendamento) {

        $post = array('solicitacaoid' => $solicitacaoid, 'produtoid' => $produtoId, 'quantidade' => $quantidade);

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $novaProdutoSolicitacao = $tProdutoSolicitacao->createRow();
        $novaProdutoSolicitacao->setFromArray($post);
        $novaProdutoSolicitacao->save();


        //atualiza a data de agendamento da solicitação
        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($solicitacaoid);

        $post = ['data_agendamento' => $data_agendamento];
        
        $solicitacao->current()->setFromArray($post);
        $solicitacao->current()->save();
    }

}

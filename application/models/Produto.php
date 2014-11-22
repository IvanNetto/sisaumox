<?php

class Produto extends Zend_Db_Table_Row_Abstract {

    public function listarProdutos() {

        $tProduto = new DbTable_Produto();
        return $produtos = $tProduto->fetchAll();
    }

    public function findProdutoById($id) {

        $tProduto = new DbTable_Produto();
        return $listaDeProdutos = $tProduto->find($id);
    }

    public function inserirProduto($post) {

        $tProduto = new DbTable_Produto();
        $novoProduto = $tProduto->createRow();
        $novoProduto->setFromArray($post);
        $novoProduto->save();
    }

    public function editarProduto($post, $produto) {

        $produto->current()->setFromArray($post);
        $produto->current()->save();
    }

    public function findProdutoByCategoriaid($categoriaid) {

        $tProduto = new DbTable_Produto();
        $query = $tProduto->select()
            ->where('categoriaid = (?)', $categoriaid);

        return $produtos = $tProduto->fetchAll($query);
    }

    public function atualizarEstoque($produtos, $operacao, $quantidade) {

        $i = 0;
        foreach ($produtos as $produto) {

            $objetoProduto = $this->findProdutoById($produto)->current();
            $quantidadecorrente = $objetoProduto->quantidade;

            $quant = $quantidade[$i];

            if ($operacao == '-')
            $quantidadecorrente = $quantidadecorrente - $quant;

            $post = ['quantidade' => $quantidadecorrente];

            $objetoProduto->setFromArray($post);
            $objetoProduto->save();
            $i++;
        }
    }
    
    public function atualizarEstoqueComProdutosRejeitados($produtos, $produtoSolicitacao){
        
        foreach ($produtos as $produto){
    
            $quantidadeRejeitada = $produto->quantidade;
            
            $tProdutoSolicitacao = new Produtosolicitacao;
            $produtoSolicitacao = $tProdutoSolicitacao->findByProdutoESolicitacao($produtoSolicitacao->solicitacaoid, $produto->produtoid);
            
            $quantidadeParcial = $produtoSolicitacao->current()->aprovacao_parcial;
            
            $tProduto = new DbTable_Produto();
            $produtoCorrente = $tProduto->find($produto->produtoid)->current();
            $quantidadeExistente = $produtoCorrente->quantidade;
            
            $quantidade = $quantidadeRejeitada + $quantidadeExistente - $quantidadeParcial;
            
            $produtoCorrente->setFromArray(['quantidade' => $quantidade]);
            $produtoCorrente->save();
            
            
        }
        
    }

    //usado na rejeição da solicitação toda
    public function devolverItemProEstoque($produtodevolvido){

        $tDevolucao = new DbTable_Devolucao;
        $devolucao= $tDevolucao->somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtodevolvido);

        $quantidadeDevolvida = $devolucao[0]['quantidade'];

        $tProduto = new DbTable_Produto;
        $produto = $tProduto->find($produtodevolvido);

        $quantidadeAtualizada = $produto->current()->quantidade + $quantidadeDevolvida;

        $produto->current()->setFromArray(['quantidade' => $quantidadeAtualizada]);
        $produto->current()->save();

    }

    public function atualizarEstoqueComProdutoDevolvido($produto, $quantidade){

        $tProduto = new DbTable_Produto;
        $produto = $tProduto->find($produto);

        $quantidadeNova = $produto->current()->quantidade + $quantidade;

        $produto->current()->setFromArray(['quantidade' => $quantidadeNova]);
        $produto->current()->save();


    }
    
    public function atualizarEstoqueComProdutosCancelados($produtoSolicitacao, $solicitacaoId){
        
        foreach ($produtoSolicitacao as $linha){
            
                $quantidadeQueSoma = $linha->quantidade;
                
                $produtoId = $linha->produtoid;
               
                $tProduto = new DbTable_Produto;
                $produto = $tProduto->find($produtoId);
                
                $quantidadeAtual = $produto->current()->quantidade;
                
                $quantidadeAtualizada = $quantidadeQueSoma + $quantidadeAtual;
                
                $produto->current()->setFromArray(['quantidade' => $quantidadeAtualizada]);
                $produto->current()->save();
        }
    }
}

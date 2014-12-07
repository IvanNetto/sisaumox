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

    public function atualizarEstoqueComProdutosRejeitados($produtos, $produtoSolicitacao) {

        $solicitacaoid = $produtoSolicitacao->solicitacaoid;

        foreach ($produtos as $produto) {

            $quantidadeRejeitada = $produto->quantidade;

            $tProdutoSolicitacao = new Produtosolicitacao;
            $produtoSolicitacao = $tProdutoSolicitacao->findByProdutoESolicitacao($produto->produtoid, $solicitacaoid);

            $quantidadeParcial = $produtoSolicitacao[0]['aprovacao_parcial'];

            $tProduto = new DbTable_Produto();
            $produtoCorrente = $tProduto->find($produto->produtoid)->current();

            $quantidadeExistente = $produtoCorrente->quantidade;

            $quantidade = $quantidadeRejeitada + $quantidadeExistente - $quantidadeParcial;

            $produtoCorrente->setFromArray(['quantidade' => $quantidade]);
            $produtoCorrente->save();

            $produtoSolicitacao->current()->setFromArray(['aprovacao_parcial' => 0]);
            $produtoSolicitacao->current()->save();
        }
    }

    //usado na rejeição da solicitação toda
    //usardo também na devolução de item
    public function devolverItemProEstoque($produtodevolvido, $produtoSolicitacaoId) {
        
        $tDevolucao = new DbTable_Devolucao;
        $devolucao = $tDevolucao->somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtoSolicitacaoId);
        
        $quantidadeDevolvida = $devolucao[0]['quantidade'];
        
        $tProduto = new DbTable_Produto;
        $produto = $tProduto->find($produtodevolvido);

        $quantidadeAtualizada = $produto->current()->quantidade + $quantidadeDevolvida;
        
        $produto->current()->setFromArray(['quantidade' => $quantidadeAtualizada]);
        $produto->current()->save();
    }

    public function atualizarEstoqueComProdutoDevolvido($produto, $quantidadeFinal, $statusDevolucaoCriada) {
        
         //por acaso vai ser diferente nas outras situações (esse if é para não deixar atualizar o estoque quando o cara cria a devolução)
          if ($statusDevolucaoCriada <> 'NOVA') {

            $tProduto = new DbTable_Produto;
            $produto = $tProduto->find($produto);
    
            $produto->current()->setFromArray(['quantidade' => $quantidadeFinal]);
            $produto->current()->save();
        }
        
    }

    public function atualizarEstoqueComProdutosCancelados($produtoSolicitacao, $solicitacaoId) {

        foreach ($produtoSolicitacao as $linha) {

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

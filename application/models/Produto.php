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

        foreach ($produtos as $produto) {

            $objetoProduto = $this->findProdutoById($produto)->current();
            $quantidadecorrente = $objetoProduto->quantidade;
            $quanditademinima = $objetoProduto->quantidademinima;



            if ($quantidadecorrente >= $quantidade) {


                if ($operacao == '-') {
                    $quantidadecorrente = $quantidadecorrente - $quantidade;
                }
                $post = ['quantidade' => $quantidadecorrente];
                //var_dump($post);
                $objetoProduto->setFromArray($post);
                $objetoProduto->save();

                if ($quantidadecorrente <= $quanditademinima) {

                    return $mensagem = "Solicitação enviada com sucesso, mas a quantidade mínima do produto em estoque foi atingida. O produto deve ser reposto para que as próximas solicitações para este produto possam ser realizadas.";
                }
            } else {

                throw new exception("A quantidade escolhida para algum(ns) item(ns) excede a quantidade mínima. Favor verificar a quantidade existente em estoque.");
            }
        }
    }

    public function devolverItemProEstoque($produtodevolvido, $id){
        
        $tDevolucao = new DbTable_Devolucao;
        $devolucao= $tDevolucao->somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtodevolvido);
        
        $quantidadeDevolvida = $devolucao[0]['quantidade'];
                
        $tProduto = new DbTable_Produto;
        $produto = $tProduto->find($produtodevolvido);
        
        $quantidadeAtualizada = $produto->current()->quantidade + $quantidadeDevolvida;
        
        $produto->current()->setFromArray(['quantidade' => $quantidadeAtualizada]);
        $produto->current()->save();
        
    }
}

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


            $quantidadecorrente = $quantidadecorrente - $quant;


            $post = ['quantidade' => $quantidadecorrente];

            $objetoProduto->setFromArray($post);
            $objetoProduto->save();
            $i++;
        }
    }

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
}

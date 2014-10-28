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
            
            if ($quantidadecorrente > $quantidade) {
                   

                if ($operacao == '-'){
                    $valor = $quantidadecorrente - $quantidade;
                }
                $post = ['quantidade' => $valor];
                //var_dump($post);
                $objetoProduto->setFromArray($post);
                $objetoProduto->save();
            }else{
                
               throw new exception("A quantidade escolhida para algum(ns) item(ns) excede a quantidade mínima. Favor verificar a quantidade existente em estoque.");
            }
            
            if ($quantidadecorrente < $quanditademinima){
                
                throw new exception("A quantidade mínima do produto em estoque foi atingida. O produto deve ser reposto para que as próximas solicitações para este produto possam ser realizadas.");
                
            }
            
            
        }
    }

}

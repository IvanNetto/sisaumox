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
                
                echo 'a quantidade escolhida para o item não poderá ser superior a quantidade existente em estoque';
                return 0;
            }
            
            if ($quantidadecorrente < $quanditademinima){
                
                echo 'quantidade mínima em estoque atingida, favor repor o item o mais breve possível';
                
            }
            
            
        }
    }

}

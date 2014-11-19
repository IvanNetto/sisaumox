<?php

class Produtocompra extends Zend_Db_Table_Row_Abstract {

    public function findByProdutoECompra($compraId, $produtoId) {

        $tProdutoCompra = new DbTable_Produtocompra();
        $query = $tProdutoCompra->select()
                ->where('compraid = (?)', $compraId)
                ->where('produtoid = (?)', $produtoId);

        return $tProdutoCompra->fetchAll($query);
    }

    public function findByCompra($compraId) {

        $tProdutoCompra = new DbTable_Produtocompra();
        $query = $tProdutoCompra->select()
                ->where('compraid = (?)', $compraId);

        return $tProdutoCompra->fetchAll($query);
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

    public function deletarItemDoCarrinho($produtoId, $produtoCompraId) {

        $tProdutoCompra = new DbTable_Produtocompra;

        $where[] = $tProdutoCompra->getAdapter()->quoteInto('id = (?)', $produtoCompraId);
        $where[] = $tProdutoCompra->getAdapter()->quoteInto('produtoid = (?)', $produtoId);

        $tProdutoCompra->delete($where);
    }

    public function limparCarrinhoDeCompras($compraId) {

        $tProdutoCompra = new DbTable_Produtocompra();
        $where = $tProdutoCompra->getAdapter()->quoteInto('compraid = (?)', $compraId);

        $tProdutoCompra->delete($where);
    }


    public function atualizarProdutoCompra($objProdutoCompra, $arrayQuantidade, $arrayValorUnitario) {

        $i =0;
        foreach ($objProdutoCompra as $produtoCompra){
        
            $tProdutoCompra = new Produtocompra;
            $linha = $tProdutoCompra->findByProdutoECompra($produtoCompra->compraid, $produtoCompra->produtoid);
            
            $quantidade = $arrayQuantidade[$i];
            $valorUnitario = $arrayValorUnitario[$i];
            
            $post = ['quantidade' => $quantidade, 'valor_unitario' => $valorUnitario];
            
            $linha->current()->setFromArray($post);           
            $linha->current()->save();
            $i++;
        }
    }

    public function reporItensNoEstoque($produtosComprados, $compraId){

        foreach  ($produtosComprados as $produtoComprado){

            $quantidadeComprada = $produtoComprado->quantidade;

            $tProduto = new DbTable_Produto();
            $produtoDoEstoque = $tProduto->find($produtoComprado->produtoid);

            $quantidadeAntiga = $produtoDoEstoque->current()->quantidade;

            $quantidadeAtualizada = $quantidadeComprada + $quantidadeAntiga;

            $post = ['quantidade' => $quantidadeAtualizada];

            $produtoDoEstoque->current()->setFromArray($post);
            $produtoDoEstoque->current()->save();

        }
            //atualizar compra com data_conclusao e status
            $tCompra = new DbTable_Compra;
            $objCompra = $tCompra->find($compraId)->current();

            $post = ['data_conclusao' => $data_conclusao = date('d/m/Y'),'status' => 'concluida'];

            $compra = new Compra;
            $compra->atualizarCompra($objCompra, $post);

    }
    
    public function inserirEntregaParcial($post, $produtoCompra){
        
        $produtoCompra->current()->setFromArray($post);
        $produtoCompra->current()->save();
        
        
    }
    
    public function atualizarEstoqueComEntregaParcial($produtoid, $quantidadeNova){
        
        $tProduto = new DbTable_Produto;
        $produto = $tProduto->find($produtoid);
        
        $post = ['quantidade' => $quantidadeNova];
        
        $produto->current()->setFromArray($post);
        $produto->current()->save();
        
    }
}

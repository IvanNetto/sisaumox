<?php

class Produtosolicitacao extends Zend_Db_Table_Row_Abstract {

    public function findByProdutoESolicitacao($solicitacaoid, $produtoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $query = $tProdutoSolicitacao->select()
            ->where('solicitacaoid = (?)', $solicitacaoid)
            ->where('produtoid = (?)', $produtoid);

        return $tProdutoSolicitacao->fetchAll($query);
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

    public function inserirProdutoSolicitacao($solicitacaoid, $produtosescolhidos) {

        for ($i = 0; $i < count($produtosescolhidos); $i++) {

            $jahExisteOItemNoCarrinho = $this->findByProdutoESolicitacao($solicitacaoid, $produtosescolhidos[$i])->current();

            if ($jahExisteOItemNoCarrinho <> null) {

                throw new exception("Você já moveu este item para o carrinho de solicitações!");
                return $produtosescolhidos[$i];
            } else {

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

    public function limparCarrinhoDeSolicitacao($solicitacaoid) {

        $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
        $where = $tProdutoSolicitacao->getAdapter()->quoteInto('solicitacaoid = (?)', $solicitacaoid);

        $tProdutoSolicitacao->delete($where);

    }

    public function registrarQuantidadeDoProdutoNaSolicitacao($produtos, $arrayQuantidade, $solicitacaoid) {

        $i =0;
        foreach ($produtos as $produto) {

            $itemSelecionado = $this->findByProdutoESolicitacao($solicitacaoid, $produto)->current();

            $quantidade = $arrayQuantidade[$i];

            $post = ['quantidade' => $quantidade];

            $itemSelecionado->setFromArray($post);
            $itemSelecionado->save();
            $i++;
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

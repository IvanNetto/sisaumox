<?php

class DevolucaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {
        
    }

    public function listardevolucaoAction() {

        $tDevolucao = new DbTable_Devolucao();
        $listaDeDevolucoes = $tDevolucao->listarNovasDevolucoes();

        $this->view->listaDeDevolucoes = $listaDeDevolucoes;
    }

    public function listardevolucaoantigaAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $status = 'recebida';

        $tDevolucao = new DbTable_Devolucao();
        $devolucoesAntigas = $tDevolucao->listarDevolucoesAntigas($usuarioId);

        $this->view->devolucoesantigas = $devolucoesAntigas;
        $this->view->usuarioid = $usuarioId;
    }

    public function inserirAction() {

        if (!($this->getRequest()->isPost())) {

            $solicitacaoid = $this->_getParam("solicitacaoid");
            $produtoid = $this->_getParam("produtoid");
            $data = $this->_getParam("data");
            $produtosolicitacaoid = $this->_getParam("produtosolicitacaoid");
            $status = $this->_getParam("status");

            $this->view->solicitacaoid = $solicitacaoid;
            $this->view->produtoid = $produtoid;
            $this->view->data = $data;
            $this->view->produtosolicitacaoid = $produtosolicitacaoid;
            $this->view->status = $status;
        } else {

            $solicitacaoid = $_POST['solicitacaoid'];
            $produtoid = $_POST['produtoid'];
            $data = $_POST['data'];
            $produtosolicitacaoid = $_POST['produtosolicitacaoid'];
            $status = $_POST['status'];
            $quantidadeEscolhida = $_POST['quantidade'];

            $tProdutoSolicitacao = new Produtosolicitacao();
            $produtoSolicitacao = $tProdutoSolicitacao->findByProdutoESolicitacao($produtoid, $solicitacaoid);

            //retorna quantidade solicitada em produtosolicitacao
            $quantidadesolicitada = $produtoSolicitacao->current()->quantidade;

            //verifica se já existe quantidade devolvida
            $tDevolucao = new DbTable_Devolucao();
            $devolucao = $tDevolucao->somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtosolicitacaoid);

            $quantidadetotal = $quantidadeEscolhida;

            if ($devolucao[0]['quantidade'] <> null) {

                $quantidadeAntigaDevolvida = $devolucao[0]['quantidade'];

                $quantidadetotal = $quantidadeescolhida + $quantidadeAntigaDevolvida;
            }

            if ($quantidadetotal <= $quantidadesolicitada) {

                $post = array('produtosolicitacaoid' => $produtosolicitacaoid, 'data_devolucao' => $data, 'quantidade_devolvida' => $quantidadeEscolhida,
                    'status_devolucao' => $status);

                $tDevolucao = new Devolucao();
                $tDevolucao->inserirDevolucao($post);

                //retorna o id da devolução inserida
                $objDevolucao = new DbTable_Devolucao();
                $devolucao = $objDevolucao->findDevolucaoByProdutoSolicitacao($produtosolicitacaoid);

                $statusDevolucaoCriada = $devolucao[0]['status_devolucao'];

                $quantidadeFinal = $quantidadetotal - $quantidadeAntigaDevolvida;

                $tProduto = new Produto();
                $tProduto->atualizarEstoqueComProdutoDevolvido($produtoid, $quantidadeFinal, $statusDevolucaoCriada);

                $this->flashMessenger->addMessage(array('success' => "Esta devolução foi encaminhada para análise com sucesso, acompanhe sua análise!"));
            } else {

                $this->flashMessenger->addMessage(array('danger' => "Quantidades devolvidas não podem ultrapassar a quantidade solicitada para o produto!"));
            }

            return $this->_helper->redirector->gotoSimple('listarhistorico', 'solicitacao');
        }
    }

    public function atualizardevolucaoAction() {

        $id = $this->_getParam("id_devolucao");
        $status = $this->_getParam("status");
        $gerenteAprovador = $this->_getParam("gerente_responsavel");
        $data_atualizacao_status = $this->_getParam("data_atualizacao_status");
        $produtoSolicitacaoId = $this->_getParam("produtosolicitacaoid");

        $post = ['status_devolucao' => $status, 'gerente_responsavel' => $gerenteAprovador, 'data_atualizacao_status' => $data_atualizacao_status];

        $tDevolucao = new Devolucao();
        $tDevolucao->atualizarDevolucao($id, $post);

        if ($status == 'aprovada') {

            $tDevolucao = new DbTable_Devolucao;
            $produtoDevolvido = $tDevolucao->findProdutoByDevolucao($id);

            $tProduto = new Produto();
            $tProduto->devolverItemProEstoque($produtoDevolvido[0]['id'], $produtoSolicitacaoId);
        }

        return $this->_helper->redirector('listardevolucao');
    }

}

<?php

class CompraController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {

        $status = ['nova', 'aguardando fornecedor'];

        //Retorna as solicitações do usuário logado que estejam ativas
        $tCompra = new Compra();
        $listaDeCompras = $tCompra->listarComprasAtivas($status);

        $this->view->listaDeCompras = $listaDeCompras;
    }

    public function inserirAction() {
  
        $gerenteResponsavel = Zend_Auth::getInstance()->getIdentity()->id;
        $status = "nova";

        $post = array(
            'gerente_responsavel' => $gerenteResponsavel,
            'status' => $status
        );

        try {

            $tCompra = new Compra();
            $tCompra->inserir($post);

            $this->flashMessenger->addMessage(array('success' => "Uma solicitação de compra foi criada com sucesso! O próximo passo é iniciar um pedido de compra para um fornecedor!"));
        } catch (Exception $e) {

            $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        return $this->_helper->redirector('listar');
    }

    public function buscarsolicitacaoAction() {

        $solicitacaoid = $_POST['solicitacaoid'];

        try {
            $tSolicitacao = new DbTable_Solicitacao;
            $solicitacao = $tSolicitacao->find($solicitacaoid);

            $this->view->solicitacao = $solicitacao;
        } catch (Exception $ex) {

            $this->flashMessenger->addMessage(array('danger' => "Solicitação não encontrada!"));
        }
    }

    public function listargerenteAction() {

        $tSolicitacao = new Solicitacao();
        $listaDeSolicitacoes = $tSolicitacao->listarSolicitacoesGerente();


        if (!($listaDeSolicitacoes)) {

            $this->flashMessenger->addMessage(array('success' => "Não existe solicitações pendentes na base de dados."));
        } else {

            $this->view->listaDeSolicitacoes = $listaDeSolicitacoes;
        }
    }

    public function listarhistoricoAction() {
        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

        $tSolicitacao = new Solicitacao();
        $historico = $tSolicitacao->listarHistorico($usuarioId);

        $this->view->historico = $historico;
    }

    public function listaragendadasAction() {

        $tSolicitacao = new Solicitacao();
        $agendadas = $tSolicitacao->listarAgendadas();

        $this->view->agendadas = $agendadas;
    }

    public function listarreprovadasAction() {

        $tSolicitacao = new Solicitacao();
        $reprovadas = $tSolicitacao->listarReprovadas();

        $this->view->reprovadas = $reprovadas;
    }

    public function listarCanceladasAction() {

        $tSolicitacao = new Solicitacao();
        $reprovadas = $tSolicitacao->listarCanceladas();

        $this->view->canceladas = $canceladas;
    }

    public function inserirsolicitacaoagendadaAction() {

        $produtoId = $this->_getParam("produtoid");

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = date("d/m/y");
        $status = "agendada";

        $post = array(
            'usuarioid' => $usuarioId,
            'data' => $data,
            'status' => $status
        );

        $tSolicitacao = new Solicitacao();
        $tSolicitacao->inserirSolicitacao($post);

        $this->flashMessenger->addMessage(array('success' => "Solicitação agendada com sucesso! Agora é só esperar a reposição para este item no estoque!"));

        $param = ['produtoid' => $produtoId];
        return $this->forward('inserirprodutoemsolicitacaoagendada', 'produtosolicitacao', null, $param);
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($id);

        try {
            $solicitacao->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }

    public function cancelarsolicitacaoAction() {
        $solicitacaoid = $this->_getParam("solicitacaoid");
        $gerente_responsavel = $this->_getParam("gerente_responsavel");

        $tProdutosolicitacao = new Produtosolicitacao();
        $produtosolicitacao = $tProdutosolicitacao->findBySolicitacao($solicitacaoid);

        $produtosolicitacao->current()->delete();

        $status = 'cancelada';
        $tSolicitacao = new Solicitacao();
        $tSolicitacao->atualizarStatus($solicitacaoid, $status, $gerente_responsavel);

        $this->flashMessenger->addMessage(array('success' => "Solicitação cancelada com sucesso!"));

        return $this->_helper->redirector('listar');
    }

    public function inserirobservacaoAction() {

        $idDevolucao = $solicitacaoid = $this->_getParam("id_devolucao");


        $solicitacaoid = $this->_getParam("solicitacaoid");

        $status = $this->_getParam("status");
        $gerente_responsavel = $this->_getParam("gerente_responsavel");


        $param = ['solicitacaoid' => $solicitacaoid];

        $this->view->solicitacaoid = $solicitacaoid;

        if ($_POST) {

            $observacao = $_POST['observacao'];
            $data_atualizacao_status = $this->_getParam("data_atualizacao_status");

            if ($idDevolucao) {

                $tDevolucao = new DbTable_Devolucao();
                $devolucao = $tDevolucao->find($idDevolucao);

                $post = (['observacao' => $observacao, 'status_devolucao' => 'reprovada', 'data_atualizacao_status' => $data_atualizacao_status, 'gerente_responsavel' => $gerente_responsavel]);

                $devolucao->current()->setFromArray($post);
                $devolucao->current()->save();
            } else {
                $tSolicitacao = new DbTable_Solicitacao();
                $solicitacao = $tSolicitacao->find($solicitacaoid);

                $post = (['observacao' => $observacao, 'status' => $status, 'data_atualizacao_status' => $data_atualizacao_status, 'gerente_responsavel' => $gerente_responsavel]);

                $solicitacao->current()->setFromArray($post);
                $solicitacao->current()->save();


                $this->forward('atualizarprodutosesolicitacao', 'produtosolicitacao', null, $param);
            }


            //pra onde vou se for devolução?
        }
    }

}

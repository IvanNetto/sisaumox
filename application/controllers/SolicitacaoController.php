<?php

class SolicitacaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        $perfil = Zend_Auth::getInstance()->getIdentity()->perfilid;

        if ($perfil == 1) {

            return $this->_helper->redirector('listar');
        } elseif ($perfil == 2) {


            return $this->_helper->redirector('listargerente');
        } elseif ($perfil == 3) {


            return $this->_helper->redirector('listargerente');
        }
    }

    /*
     * Lista solicitações ATIVAS do usuario logado 
     */

    public function listarAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

//Armazena os status de solicitações que podem aparecer na list principal do usuário solicitante         
        $solicitacoesAtivas = ['nova', 'rejeitada', 'aprovada', 'em analise', 'entregue', 'agendada'];

//Retorna as solicitações do usuário logado que estejam ativas
        $tSolicitacao = new Solicitacao();
        $listaDeSolicitacoes = $tSolicitacao->findSolicitacoesAtivasByUsuario($usuarioId, $solicitacoesAtivas);


        if (!($listaDeSolicitacoes)) {

            $this->flashMessenger->addMessage(array('success' => "Você não tem solicitações ativas. Crie a sua a qualquer momento!"));
        } else {

            $this->view->listaDeSolicitacoes = $listaDeSolicitacoes;
            $this->view->usuario = $usuarioId;
        }
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

    public function listarcanceladasAction() {

        $tSolicitacao = new Solicitacao();
        $canceladas = $tSolicitacao->listarCanceladas();

        $this->view->canceladas = $canceladas;
    }

    public function inserirAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = date("d/m/Y");
        $status = "nova";

        $post = array(
            'usuarioid' => $usuarioId,
            'data' => $data,
            'status' => $status
        );

        try {

            $tSolicitacao = new Solicitacao();
            $tSolicitacao->inserirSolicitacao($post, $usuarioId);

            $this->flashMessenger->addMessage(array('success' => "Solicitação criada com sucesso! Você já pode iniciar sua lista de solicitações agora!"));
        } catch (Exception $e) {

            $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        return $this->_helper->redirector('listar');
    }

    public function inserirsolicitacaoagendadaAction() {

        $produtoId = $this->_getParam("produtoid");

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = date("d/m/Y");
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

    public function deletaragendadaAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $tSolicitacao = new DbTable_Solicitacao();
        $maxSolicitacaoId = $tSolicitacao->mostrarUltimaSolicitacaoAgendada();

        $tSolicitacao = new Solicitacao();
        $tSolicitacao->deletarUltimaAgendada($maxSolicitacaoId[0]['id']);

        return $this->_helper->redirector('listar');
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

//atualiza o estoque antes de deletar
        $produto = new Produto();
        $produto->atualizarEstoqueComProdutosCancelados($produtosolicitacao, $solicitacaoid);

//deleta linhas da t_produto_solicitacao
        $tProdutosolicitacao->limparCarrinhoDeSolicitacao($solicitacaoid);

        $status = 'cancelada';
        $tSolicitacao = new Solicitacao();
        $tSolicitacao->atualizarStatus($solicitacaoid, $status, $gerente_responsavel);

//atualizar o estoque retirando os valores que foram solicitados !!!!!

        $this->flashMessenger->addMessage(array('success' => "Solicitação cancelada com sucesso!"));

        return $this->_helper->redirector('listar');
    }

    public function inserirobservacaoAction() {


        $idDevolucao = $this->_getParam("id_devolucao");
        $solicitacaoid = $this->_getParam("solicitacaoid");
        $produtosolicitacaoid = $this->_getParam("produtosolicitacaoid");
        $status = $this->_getParam("status");
        $gerente_responsavel = $this->_getParam("gerente_responsavel");

        $param = ['solicitacaoid' => $solicitacaoid, 'produtosolicitacaoid' => $produtosolicitacaoid, 'id_devolucao' => $idDevolucao];

        $this->view->produtosolicitacaoid = $produtosolicitacaoid;
        $this->view->solicitacaoid = $solicitacaoid;

        if ($_POST) {

            $observacao = $_POST['observacao'];
            $data_atualizacao_status = $this->_getParam("data_atualizacao_status");
            if ($idDevolucao) {
                $tDevolucao = new DbTable_Devolucao();
                $devolucao = $tDevolucao->find($idDevolucao);
                $post = (['observacao' => $observacao, 'status_devolucao' => 'reprovada', 'data_atualizacao_status' => $data_atualizacao_status, 'gerente_responsavel' => $gerente_responsavel, 'status' => $status]);
                $devolucao->current()->setFromArray($post);
                $devolucao->current()->save();

                $this->forward('atualizardevolucao', 'produtosolicitacao', null, $param);
            } else {

                $tSolicitacao = new DbTable_Solicitacao();

                $solicitacao = $tSolicitacao->find($_POST['solicitacaoid']);

                if (($status == null) && ($observacao <> null)) {

                    $post = (['observacao' => $observacao]);
                    $solicitacao->current()->setFromArray($post);
                    $solicitacao->current()->save();

                    $parametro = ['solicitacaoid' => $solicitacaoid];

                    $this->forward('resumodesolicitacao', 'produtosolicitacao', null, $parametro);
                } else {
                    
                    $post = (['observacao' => $observacao, 'status' => $status, 'data_atualizacao_status' => $data_atualizacao_status, 'gerente_responsavel' => $gerente_responsavel]);
                    $solicitacao->current()->setFromArray($post);
                    $solicitacao->current()->save();

                    $this->forward('atualizarprodutosesolicitacao', 'produtosolicitacao', null, $param);
                }
            }
        }
    }

}

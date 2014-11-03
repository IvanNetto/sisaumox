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


            return $this->_helper->redirector('listaradmin');
        }
    }

    /*
     * Lista solicitações ATIVAS do usuario logado 
     */

    public function listarAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

        //Armazena os status de solicitações que podem aparecer na list principal do usuário solicitante         
        $solicitacoesAtivas = ['nova', 'rejeitada', 'aprovada', 'em analise'];

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

    public function inserirAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $data = date("d/m/y");
        $status = "nova";

        $post = array(
            'usuarioid' => $usuarioId,
            'data' => $data,
            'status' => $status
        );

        try {

            $tSolicitacao = new Solicitacao();
            $novaSolicitacao = $tSolicitacao->inserirSolicitacao($post, $usuarioId);

            $this->flashMessenger->addMessage(array('success' => "Solicitação criada com sucesso! Você já pode iniciar sua lista de solicitações agora!"));
        
            
        } catch (Exception $e) {

            $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        return $this->_helper->redirector('listar');
//        $perfil = Zend_Auth::getInstance()->getIdentity()->perfilid;
//        if ($perfil == 1){
//            
//            return $this->_helper->redirector('listar');
//        
//            
//        }elseif ($perfil == 2) {
//            
//            
//            return $this->_helper->redirector('listargerente');
//            
//        }elseif ($perfil == 3) {
//            
//            
//            return $this->_helper->redirector('listaradmin');
//            
//        }
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
        $novaSolicitacao = $tSolicitacao->inserirSolicitacao($post);
        
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

        //deletando da produtosolicitacao



        return $this->_helper->redirector('listar');
    }

    public function cancelarsolicitacaoAction()
    {
        $solicitacaoid = $this->_getParam("solicitacaoid");

        $tProdutosolicitacao = new Produtosolicitacao();
        $produtosolicitacao = $tProdutosolicitacao->findBySolicitacao($solicitacaoid);

        $produtosolicitacao->current()->delete();

        $status = 'cancelada';
        $tSolicitacao = new Solicitacao();
        $tSolicitacao->atualizarStatus($solicitacaoid, $status);

        $this->flashMessenger->addMessage(array('success' => "Solicitação cancelada com sucesso!"));

        return $this->_helper->redirector('listar');

    }

}

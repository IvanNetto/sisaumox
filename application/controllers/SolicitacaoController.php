<?php

class SolicitacaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    /*
     * Lista solicitações ATIVAS do usuario logado 
     */

    public function listarAction() {

        //Armazena os status de solicitações que podem aparecer na list principal do usuário solicitante         
        $solicitacoesAtivas = ['nova', 'rejeitada', 'aprovada', 'em analise', 'agendada', 'entregue'];

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

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
    
    
    public function listarhistoricoAction(){
        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

        $tSolicitacao = new Solicitacao();
        $historico = $tSolicitacao->listarHistorico($usuarioId);
        
        $this->view->historico = $historico;
        
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
    

}

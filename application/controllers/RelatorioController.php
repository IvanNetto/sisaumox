<?php

class RelatorioController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {
        
    }

    public function solicitacoesporperiodoAction() {

        if (!($_POST)) {

            $this->view->titulo = 'relatorio';
        } else {


            $data1 = $_POST['data1'];
            $data2 = $_POST['data2'];


            if (($data1) && ($data2)) {

                $tSolicitacao = new DbTable_Solicitacao();
                $solicitacoes = $tSolicitacao->RelatorioDeSolicitacoesPorPeriodo($data1, $data2);
                
                $this->view->solicitacoes = $solicitacoes;
            }
        }
    }
    
    public function solicitacoesporusuarioAction(){
        
        $usuario = $this->getParam('usuarioid');
        $login = $this->getParam('login');
        
        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacoes = $tSolicitacao->relatorioDeSolicitacoesPorUsuario($usuario);
        
        
        
        $this->view->solicitacoes = $solicitacoes;
        $this->view->login = $login;
        
    }

}

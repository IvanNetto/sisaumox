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
            $status = $_POST['status'];

            if (($data1) && ($data2)) {

                $tSolicitacao = new DbTable_Solicitacao();
                $solicitacoes = $tSolicitacao->RelatorioDeSolicitacoesPorPeriodo($data1, $data2, $status);

                $this->view->solicitacoes = $solicitacoes;
            }
        }
    }

    public function solicitacoesporusuarioAction() {

        $usuario = $this->getParam('usuarioid');
        $login = $this->getParam('login');

        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacoes = $tSolicitacao->relatorioDeSolicitacoesPorUsuario($usuario);



        $this->view->solicitacoes = $solicitacoes;
        $this->view->login = $login;
    }

    public function produtosmaissolicitadosAction() {

        if (!($_POST)) {

            $this->view->titulo = 'relatorio';
        } else {

            $data1 = $_POST['data1'];
            $data2 = $_POST['data2'];

            $tProduto = new DbTable_Produto();
            $produtos = $tProduto->produtosMaisSolicitadosPorPeriodo($data1, $data2);
            
            $this->view->produtos = $produtos;
        }
    }

    public function fornecedoresporprodutoAction() {

        $tFornecedor = new DbTable_Fornecedor();
        $fornecedores = $tFornecedor->listarFornecedoresPorProduto();

        $this->view->fornecedores = $fornecedores;
    }

    public function comprasporperiodoAction() {

        if (!($_POST)) {

            $this->view->titulo = 'relatorio';
        } else {

            $data1 = $_POST['data1'];
            $data2 = $_POST['data2'];

            $tCompra = new DbTable_Compra();
            $compras = $tCompra->comprasPorPeriodo($data1, $data2);

            $this->view->compras = $compras;
        }
    }

    public function usuariosporperfilAction() {

        if (!($_POST)) {

            $this->view->titulo = 'relatorio';
        } else {

            $perfil = $_POST['perfil'];

            $tPerfil = new DbTable_Usuario();
            $usuarios = $tPerfil->usuariosPorPerfil($perfil);

            $this->view->usuarios = $usuarios;
        }
    }

}

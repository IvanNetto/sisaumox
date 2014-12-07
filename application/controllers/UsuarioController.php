<?php

class UsuarioController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    //lista usuários mostrando login e nome da pessoa
    public function listarAction() {

        $relatorio = $this->getParam('relatorio');
        
        $tUsuario = new Usuario();
        $listaDeUsuarios = $tUsuario->listarUsuarios();

        $tPessoa = new Pessoa();
        $pessoas = $tPessoa->listarPessoas();

        $tPerfil = new Perfil();
        $perfis = $tPerfil->listarPerfis();

        $this->view->listaDeUsuarios = $listaDeUsuarios->toArray();
        $this->view->pessoas = $pessoas->toArray();
        $this->view->perfis = $perfis->toArray();
        $this->view->relatorio = $relatorio;
        
    }

    public function inserirAction() {

        $tPessoa = new Pessoa();
        $pessoas = $tPessoa->listarPessoas();

        $tPerfil = new Perfil();
        $perfis = $tPerfil->listarPerfis();

        $this->view->pessoas = $pessoas;
        $this->view->perfis = $perfis;

        if ($this->getRequest()->isPost()) {

            $pessoaid = $_POST['pessoaid'];
            $perfilid = $_POST['perfilid'];
            $login = $_POST['login'];
            $senha = $_POST['senha'];

            $post = array('pessoaid' => $pessoaid, 'perfilid' => $perfilid, 'login' => $login, 'senha' => $senha);
           
            try {

                $tUsuario = new Usuario();
                $novoUsuario = $tUsuario->inserirUsuario($post);

                $this->flashMessenger->addMessage(array('success' => "Um novo usuário foi cadastrado com sucesso!"));
            } catch (Exception $ex) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isso é constangedor! Por algum motivo seu usuário não foi cadastrado. Tente novamente!"));
            };
            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {

        $tPessoa = new Pessoa();
        $pessoas = $tPessoa->listarPessoas();

        $tPerfil = new Perfil();
        $perfis = $tPerfil->listarPerfis();

        $this->view->pessoas = $pessoas;
        $this->view->perfis = $perfis;

        $id = $this->_getParam("id");
        $tUsuario = new Usuario();
        $usuario = $tUsuario->findUsuarioById($id);

        $this->view->usuario = $usuario->current();

        if ($this->getRequest()->isPost()) {

            $pessoaid = $_POST['pessoaid'];
            $perfilid = $_POST['perfilid'];
            $login = $_POST['login'];
            $senha = $_POST['senha'];

            $post = array('login' => $login, 'senha' => $senha);

            try {

                $usuarioEditado = $tUsuario->editarUsuario($post, $usuario);
                $this->flashMessenger->addMessage(array('success' => "Usuário editado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor. Por algum motivo seu usuári não foi editado com sucesso. Tente novamente!"));
            };

           $this->_helper->redirector('listar');
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tUsuario = new DbTable_Usuario();
        $usuario = $tUsuario->find($id);

        try {
            $usuario->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }

    public function perfilusuarioAction() {


        $usuario = Zend_Auth::getInstance()->getIdentity()->id;

        $tUsuario = new DbTable_Usuario();
        $perfilusuario = $tUsuario->perfilUsuario($usuario);

        $this->view->perfilusuario = $perfilusuario;
    }

    public function trocadesenhaAction() {

        $usuario = Zend_Auth::getInstance()->getIdentity()->id;

        if (!$_POST) {

            $this->view->usuario = $usuario;
        } else {

            $tUsuario = new DbTable_Usuario();
            $usuario = $tUsuario->find($usuario);

            $senhaAtual = $usuario->current()->senha;

            $senha = trim($_POST['senha']);
            $novasenha = trim($_POST['novasenha']);
            $resenha = trim($_POST['resenha']);

            $post = array('senha' => $resenha);

            try {

                if ($senha == $senhaAtual) {

                    if ($novasenha == $resenha) {

                        $usuario->current()->setFromArray($post);
                        $usuario->current()->save();

                        $this->flashMessenger->addMessage(array('success' => "Troca de senha efetuada com sucesso!"));
                        return $this->_helper->redirector('perfilusuario');
                    } else {

                        $this->flashMessenger->addMessage(array('danger' => "Confirmação de senha não confere!"));
                        return $this->_helper->redirector('trocadesenha');
                    }
                } else {

                    $this->flashMessenger->addMessage(array('danger' => "Senha atual não existe!"));
                    return $this->_helper->redirector('trocadesenha');
                }
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! Sua senha não foi trocada. Tente novamente!"));
                return $this->_helper->redirector('trocadesenha');
            };
        }
    }

}

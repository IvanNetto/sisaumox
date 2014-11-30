<?php

class FornecedorController extends Zend_Controller_Action {

    protected $flashMessenger;


    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger",$this->flashMessenger);

    }

    public function indexAction() {

        return $this->_helper->redirector('listar');

    }

    public function listarAction() {

        $tFornecedor = new Fornecedor();
        $listaDeFornecedores = $tFornecedor->listarFornecedores();

        $this->view->listaDeFornecedores = $listaDeFornecedores->toArray();
        
    }

    public function inserirAction() {

        $perfil = new Fornecedor();
        
        if ($this->getRequest()->isPost()) {


            $post = array('id'=> $_POST['id'], 'razao_social' => $_POST['razao_social'], 'inscricao_estadual'=> $_POST['inscricao_estadual'], 'telefone1' => $_POST['telefone1'], 'telefone2' => $_POST['telefone2'],
                    'rua' => $_POST['rua'], 'bairro' => $_POST['bairro'], 'numero' => $_POST['numero'], 'complemento' => $_POST['complemento'], 
                    'cidade' => $_POST['cidade'], 'uf' => $_POST['uf'], 'contato' => $_POST['contato'], 'email' => $_POST['email']);
            
            try {
                $novoFornecedor = $perfil->inserirFornecedor($post);                
                $this->flashMessenger->addMessage(array( 'success' => "Um novo fornecedor foi cadastrado com sucesso!"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "Bem, isto é constrangedor. Um novo fornecedor não foi cadastrado. Tente novamente!"));

            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {
        
        $id = $this->_getParam("id");
        
        if (isset($_POST['cnpj'])){
            
        $semPonto = explode('.', $_POST['cnpj']);
        $var = $semPonto[0] . $semPonto[1];

        $semBarra = explode('/', $semPonto[2]);
        $var = $var . $semBarra[0];

        $semTraco = explode('-', $semBarra[1]);
        $var = $var . $semTraco[0] . $semTraco[1];
        
        $id = $var;
            
        }
        
        $tFornecedor = new Fornecedor();
        $fornecedor = $tFornecedor->findFornecedorById($id);

        $this->view->fornecedor = $fornecedor->current();
        $this->view->id = $id;

        if ($this->getRequest()->isPost()) {

            
            $post = array('id'=> $_POST['id'], 'razao_social' => $_POST['razao_social'], 'inscricao_estadual' => $_POST['inscricao_estadual'], 'telefone1' => $_POST['telefone1'], 'telefone2' => $_POST['telefone2'],
                    'rua' => $_POST['rua'], 'bairro' => $_POST['bairro'], 'numero' => $_POST['numero'], 'complemento' => $_POST['complemento'], 
                    'cidade' => $_POST['cidade'], 'uf' => $_POST['uf'], 'contato' => $_POST['contato'], 'email' => $_POST['email']);

            try {
               
                $perfilEditado = $tFornecedor->editarFornecedor($post, $fornecedor);
                $this->flashMessenger->addMessage(array('success' => "O fornecedor foi editado com sucesso!"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "Bem, isto é constrangedor. O fornecedor não foi editado. Tente novamente!"));

            };

             $this->_helper->redirector('listar');
        }
    }
    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tFornecedor = new DbTable_Fornecedor();
        $perfil = $tFornecedor->find($id);

        try {

            $perfil->current()->delete();
            $this->flashMessenger->addMessage(array( 'success' => "Fornecedor excluido com sucesso!"));

        } catch (Exception $e) {

            echo $e->getMessage();
            $this->flashMessenger->addMessage(array( 'success' => "Bem, isto é constrangedor. O fornecedor não foi excluido. Tente novamente!"));
        }

        return $this->_helper->redirector('listar');
    }

}

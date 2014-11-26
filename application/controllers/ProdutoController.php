<?php

class ProdutoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {

        $tCategoria = new Categoria();
        $categorias = $tCategoria->listarCategorias();

        $tProduto = new Produto();
        $listaDeProdutos = $tProduto->listarProdutos();

        $this->view->listaDeProdutos = $listaDeProdutos->toArray();
        $this->view->categorias = $categorias->toArray();
    }

    public function inserirAction() {

        $tCategoria = new Categoria();
        $categorias = $tCategoria->listarCategorias();

        $this->view->categorias = $categorias;

        if ($this->getRequest()->isPost()) {
            $codigo = $_POST['codigo'];
            $categoriaid = $_POST['categoriaid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $quantidade = $_POST['quantidade'];
            $quantidademinima = $_POST['quantidademinima'];
            $validade = $_POST['validade'];

            $post = array('codigo' => $codigo, 'categoriaid' => $categoriaid, 'nome' => $nome, 'descricao' => $descricao,
                'quantidade' => $quantidade, 'quantidademinima' => $quantidademinima, 'validade' => $validade);

            try {
                $tProduto = new Produto();
                $novoProduto = $tProduto->inserirProduto($post);
                $this->flashMessenger->addMessage(array('success' => "O novo produto foi cadastrado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isso é constangedor! Por algum motivo seu produto não foi cadastrado. Tente novamente!"));
            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {

        $tCategoria = new Categoria();
        $categorias = $tCategoria->listarCategorias();
        $this->view->categorias = $categorias;

        $id = $this->_getParam("id");
        $tProduto = new Produto();
        $produto = $tProduto->findProdutoById($id);

        $this->view->produto = $produto->current();

        if ($this->getRequest()->isPost()) {

            $id = $_POST['id'];
            $codigo = $_POST['codigo'];
            $categoriaid = $_POST['categoriaid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $quantidade = $_POST['quantidade'];
            $quantidademinima = $_POST['quantidademinima'];
            $validade = $_POST['validade'];

            $post = array('id' => $id, 'codigo' => $codigo, 'categoriaid' => $categoriaid, 'nome' => $nome, 'descricao' => $descricao,
                'quantidade' => $quantidade, 'quantidademinima' => $quantidademinima, 'validade' => $validade);


            try {

                $produtoEditado = $tProduto->editarProduto($post, $produto);
                $this->flashMessenger->addMessage(array('success' => "tudo ok"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "ops!"));
            };

            $this->forward('index', 'produto', $post);
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tProduto = new DbTable_Produto();
        $Produto = $tProduto->find($id);

        try {
            $Produto->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }
    
    
    public function buscarprodutoporcategoriaAction(){
        
        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $categoriaid = $this->_getParam("categoriaid");

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;

        if ($categoriaid) {
            
            $tProdutosolicitacao = new DbTable_Produtosolicitacao();
            $listaItensProibidos = $tProdutosolicitacao->verificarSeJahExisteItemEmSolicitacaoAtivaDoUsuario($usuarioId);
            
            
            
            
            $tProdutoSolicitacao = new Produtosolicitacao;
            $listaDeItensPermitidos = $tProdutoSolicitacao->listarProdutosPermitidos($categoriaid, $listaItensProibidos);

            $this->view->listaDeProdutos = $listaDeItensPermitidos->toArray();
        }
       
    }
    
    public function listarprodutosindisponiveisAction(){
        

        $tProduto = new Produto();
        $produtosIndisponiveis = $tProduto->listarIndisponiveis();

        $this->view->lisdaDeIndisponiveis = $produtosIndisponiveis;
        
        
    }
    
    public function listarprodutosquantidademinimaAction(){
        

        $tProduto = new DbTable_Produto();
        $produtosIndisponiveis = $tProduto->listarIndisponiveis();

        $this->view->lisdaDeQuantidadeMinima = $produtosIndisponiveis;
        
        
    }
    
}

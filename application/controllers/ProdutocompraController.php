<?php

class ProdutocompraController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {
        
    }

    public function buscarprodutosporcategoriaAction() {

        $categoriaId = $this->_getParam("categoriaId");
        $compraId = $this->_getParam("compraId");

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;

        if ($categoriaId) {

            $tProdutoCompra = new DbTable_ProdutoCompra();
            $listaItensProibidos = $tProdutoCompra->verificarSeJahExisteItemEmCompraAtiva();

            $produtoCompra = new Produtocompra;
            $listaDeItensPermitidos = $produtoCompra->listarProdutosPermitidos($categoriaId, $listaItensProibidos);

            $this->view->listaDeProdutos = $listaDeItensPermitidos->toArray();
        }

        $this->view->compraId = $compraId;
    }

    public function inserirAction() {

        $produtosEscolhidos = ($_POST['checkbox']);
        $compraId = ($_POST['compraId']);
        $categoriaId = ($_POST['categoriaId']);
        
        try {

            $produtoCompra = new Produtocompra();
            $produtoCompra->inserirProdutoCompra($compraId, $produtosEscolhidos);
            
            $deucerto = true;
        } catch (Exception $e) {

            $deucerto = false;
        };

        $params = array('categoriaId' => $categoriaId, 'compraId' => $compraId, 'deucerto' => $deucerto);
        $this->forward('buscarprodutosporcategoria', 'produtocompra', null, $params);
    }

   public function carrinhodecomprasAction() {


        if ($this->_getParam('deucerto')) {
            $this->flashMessenger->addMessage(array('success' => "Item removido do carrinho com sucesso"));
        }

        $compraId = $this->_getParam('compraId');

        $produtoCompra = new Produtocompra();
        $conteudoDoCarrinho = $produtoCompra->exibirCarrinhoDeCompras($compraId);

        $this->view->conteudoDoCarrinho = $conteudoDoCarrinho;
        $this->view->compraId = $compraId;
    }

    public function gerarpedidodecomprasAction(){
        
        $compraId = ($_POST['compraId']);
        
        $pedidoDeCompra = new DbTable_Compra();
        $pedido = $pedidoDeCompra->gerarPedidoDeCompra($compraId);
        
        $this->view->pedidoDeCompra = $pedido;
        
        
    }
    
    public function listarfornecedoresAction(){
        
        $tFornecedor = new DbTable_Fornecedor;
        $listaDeFornecedores = $tFornecedor->fetchAll();
        
        $this->view->listaDeFornecedores = $listaDeFornecedores;
        
    }

    public function atualizarprodutosecompraAction() {

        $produtos = $_POST['produto'];
        $operacao = '+';
        $quantidade = $_POST['quantidade'];
        $data_recebimento = $this->_getParam('data_recebimento');
        $data_aprovacao = $this->_getParam('data_aprovacao');
        $gerente_responsavel = $this->_getParam('gerente_responsavel');

        //mudar
        $status = $this->_getParam('status');

        if ($_POST['compraId']) {

            $solicitacaoid = $_POST['compraId'];
        } else {
            $solicitacaoid = $this->_getParam('compraId');
        }


        if ($produtos) {

            try {
                $tProduto = new Produto();
                $mensagem = $tProduto->atualizarEstoque($produtos, $operacao, $quantidade, $_POST['compraId']);

                $tProdutosolicitacao = new Produtosolicitacao();
                $tProdutosolicitacao->registrarQuantidadeDoProdutoNaCompra($produtos, $quantidade, $solicitacaoid);

                //mensagem de estoque minimo atingido
                if ($mensagem) {
                    
                    $this->flashMessenger->addMessage(array('danger' => $mensagem));
                }
            } catch (Exception $e) {
                //gera exceção se solicicitar quantidade superior a existente em estoque
                $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
                $qtdsuperior = true;
            }
        }

        $tsolicitacao = new Solicitacao();
        if ($status == 'entregue') {
            $statusatual = 'entregue';
        } elseif ($status == 'recebida') {
            $statusatual = 'recebida';
        } else {
            $statusatual = $tsolicitacao->mostrarStatusAtual($solicitacaoid)->current()->status;
        }

        if (!($qtdsuperior))
            $tsolicitacao->atualizarStatus($solicitacaoid, $statusatual, $gerente_responsavel);


        if ($data_recebimento) {

            $tsolicitacao->atualizaDataDeRecebimento($solicitacaoid, $data_recebimento);
        }

        if ($data_aprovacao) {

            $tsolicitacao->atualizaDataDeaprovação($solicitacaoid, $data_aprovacao);
        }

        $perfil = Zend_Auth::getInstance()->getIdentity()->perfilid;

        if ($perfil == 1) {
            return $this->_helper->redirector->gotoSimple('listar', 'solicitacao');
        } elseif ($perfil == 2 || $perfil == 3) {
            return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');
        }
    }

    
}

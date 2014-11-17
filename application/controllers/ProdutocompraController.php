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
        $compraid = $this->_getParam("compraid");
        $fornecedorId = $this->_getParam("fornecedorid");
        $datapedido = $this->_getParam("datapedido");
        $status = $this->_getParam("status");
        $valortotal = $this->_getParam("valortotal");

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;
        $this->view->fornecedorid = $fornecedorId;
        $this->view->datapedido = $datapedido;
        $this->view->status = $status;
        $this->view->valortotal = $valortotal;

        if ($categoriaId) {

            $tProdutoCompra = new DbTable_Produtocompra();
            $listaItensProibidos = $tProdutoCompra->verificarSeJahExisteItemEmCompraAtiva();

            $produtoCompra = new Produtocompra;
            $listaDeItensPermitidos = $produtoCompra->listarProdutosPermitidos($categoriaId, $listaItensProibidos);

            $this->view->listaDeProdutos = $listaDeItensPermitidos->toArray();
        }

        $this->view->compraid = $compraid;
        $this->view->fornecedorid = $fornecedorId;
        
    }

    public function inserirAction() {

        $produtosEscolhidos = ($_POST['checkbox']);
        $compraid = ($_POST['compraid']);
        $categoriaId = ($_POST['categoriaId']);
        $fornecedorId = ($_POST['fornecedorid']);

        try {

            $produtoCompra = new Produtocompra();
            $produtoCompra->inserirProdutoCompra($compraid, $produtosEscolhidos);

            $deucerto = true;
        } catch (Exception $e) {

            $deucerto = false;
        };

        $params = array('categoriaId' => $categoriaId, 'compraid' => $compraid, 'fornecedorid' => $fornecedorId);
        $this->forward('buscarprodutosporcategoria', 'produtocompra', null, $params);
    }

    public function carrinhodecomprasAction() {


        if ($this->_getParam('deucerto')) {
            $this->flashMessenger->addMessage(array('success' => "Item removido do carrinho com sucesso"));
        }

        $compraid = $this->_getParam('compraid');
        $fornecedorId = $this->_getParam('fornecedorid');
        $datapedido = $this->_getParam("datapedido");
        $status = $this->_getParam("status");
        $valortotal = $this->_getParam("valortotal");

        $produtoCompra = new Produtocompra();
        $conteudoDoCarrinho = $produtoCompra->exibirCarrinhoDeCompras($compraid);

        $this->view->conteudoDoCarrinho = $conteudoDoCarrinho;
        $this->view->compraid = $compraid;
        $this->view->fornecedorid = $fornecedorId;
        $this->view->datapedido = $datapedido;
        $this->view->status = $status;
        $this->view->valortotal = $valortotal;
    }

    public function gerarpedidodecomprasAction() {

        $compraid = ($_POST['compraid']);

        $pedidoDeCompra = new DbTable_Compra();
        $pedido = $pedidoDeCompra->gerarPedidoDeCompra($compraid);

        $this->view->pedidoDeCompra = $pedido;
    }

    public function listarfornecedoresAction() {

        $tFornecedor = new DbTable_Fornecedor;
        $listaDeFornecedores = $tFornecedor->fetchAll();

        $this->view->listaDeFornecedores = $listaDeFornecedores;
    }

    public function deletarAction() {

        $compraid = $this->getParam('compraid');
        $produtoId = $this->getParam('produtoid');
        $produtoCompraId = $this->getParam('id');
        //deleta 1 item de produto do carrinho
        if ($produtoId) {

            try {

                $tProdutoCompra = new Produtocompra;
                $tProdutoCompra->deletarItemDoCarrinho($produtoId, $produtoCompraId);

                $this->flashMessenger->addMessage(array('success' => "Item removido do carrinho com sucesso"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "O item não foi removido"));
            }

            return $this->forward('carrinhodecompras', 'produtocompra', null, ['produtoid' => $produtoId, 'id' => $produtoCompraId]);
        }

        if ($compraid) {

            try {

                $tProdutoCompra = new Produtocompra;
                $tProdutoCompra->limparCarrinhoDeCompras($compraid);

                $this->flashMessenger->addMessage(array('success' => "Carrinho limpo com sucesso"));

                return $this->forward('buscarprodutosporcategoria', 'produtocompra', null, ['compraid' => $compraid]);
                
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Carrinho não foi limpo"));
                return $this->forward('carrinhodecompras', 'produtocompra', null, ['produtoid' => $produtoId, 'id' => $produtoCompraId]);
            }
        }
    }
    
    public function resumodepedidoAction(){
        
        
        $tProdutoCompra = new DbTable_Produtocompra;
        $resumoDePedido = $tProdutoCompra->fetchAll();
        
        $compraId = $resumoDePedido[0]->compraid;
        
        $this->view->resumoDePedido = $resumoDePedido;
        $this->view->compraId = $compraId;
        
    }

    public function reporitensnoestoqueAction(){

        $compraId = $this->_getParam('id');

        $produtoCompra = new Produtocompra;
        $produtosComprados = $produtoCompra->findByCompra($compraId);

        $produtoCompra->reporItensNoEstoque($produtosComprados, $compraId);

        return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');


    }

}

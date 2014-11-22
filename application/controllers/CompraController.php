<?php

class CompraController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {

        $status = ['nova', 'aguardando fornecedor'];

        $tCompra = new Compra();
        $listaDeCompras = $tCompra->listarComprasAtivas($status);

        $this->view->listaDeCompras = $listaDeCompras;
    }

    public function inserirAction() {

        $gerenteResponsavel = Zend_Auth::getInstance()->getIdentity()->id;
        $status = "nova";

        $post = array(
            'gerente_responsavel' => $gerenteResponsavel,
            'status' => $status
        );

        try {

            $tCompra = new Compra();
            $tCompra->inserir($post);

            $this->flashMessenger->addMessage(array('success' => "Uma solicitação de compra foi criada com sucesso! O próximo passo é iniciar um pedido de compra para um fornecedor!"));
        } catch (Exception $e) {

            $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        return $this->_helper->redirector('listar');
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $compraid = $this->_getParam("id");

        $tCompra = new DbTable_Compra();
        $compra = $tCompra->find($compraid);

        try {
            $compra->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }

    public function listarhistoricoAction() {
        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

        $tSolicitacao = new Solicitacao();
        $historico = $tSolicitacao->listarHistorico($usuarioId);

        $this->view->historico = $historico;
    }

    public function atualizarcompraAction() {

        $compraId = $_POST['compraid'];
        $fornecedorid = $_POST['fornecedorid'];
        $datapedido = $_POST['datapedido'];
        $status = $_POST['status'];
        $arrayQuantidade = $_POST['quantidade'];
        $arrayValorunitario = $_POST['valorunitario'];
        $valorTotal = 0;

        foreach ($arrayQuantidade as $quantidade) {

            foreach ($arrayValorunitario as $valorunitario) {

                $valunit = str_replace(',', '.', $valorunitario);
                $quant = number_format($quantidade, 2, '.', ' ');

                $valor = $valunit * $quant;
            }

            $valorTotal = $valorTotal + $valor;
        }

        //atualiza t_compra
        $post = ['fornecedorid' => $fornecedorid, 'data_pedido' => $datapedido, 'status' => $status, 'valor_total' => $valorTotal];

        $tCompra = new DbTable_Compra;
        $objTcompra = $tCompra->find($compraId)->current();

        $compra = new Compra;
        $compra->atualizarCompra($objTcompra, $post);
                
        //atualiza t_produto_compra
        $tProdutoCompra = new Produtocompra;
        $objTProdutocompra = $tProdutoCompra->findByCompra($compraId);
        $tProdutoCompra->atualizarProdutoCompra($objTProdutocompra, $arrayQuantidade, $arrayValorunitario);
        
        return $this->_helper->redirector('listar');
        
    }

}

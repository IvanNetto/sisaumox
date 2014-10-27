<?php
class DbTable_ProdutoCompra extends Zend_Db_Table_Abstract
{

	protected $_name = 'PRODUTOCOMPRA';
	protected $_rowClass = 'DbRowProdutoCompra';
        protected $_primary = 'ID';

}
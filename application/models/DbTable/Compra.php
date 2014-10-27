<?php
class DbTable_Compra extends Zend_Db_Table_Abstract
{

	protected $_name = 'COMPRA';
	protected $_rowClass = 'DbRowCompra';
        protected $_primary = 'ID';

}
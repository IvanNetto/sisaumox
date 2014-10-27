<?php
class DbTable_Fornecedor extends Zend_Db_Table_Abstract
{

	protected $_name = 'FORNECEDOR';
	protected $_rowClass = 'DbRowFornecedor';
        protected $_primary = 'ID';

}
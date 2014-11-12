<?php
class DbTable_Fornecedor extends Zend_Db_Table_Abstract
{

	protected $_name = 't_fornecedor';
	protected $_rowClass = 'Fornecedor';
        protected $_primary = 'id';

}
<?php
class DbTable_Produto extends Zend_Db_Table_Abstract
{

	protected $_name = 't_produto';
	protected $_rowClass = 'Produto';
        protected $_primary = 'id';

}
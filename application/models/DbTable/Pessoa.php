<?php
class DbTable_Pessoa extends Zend_Db_Table_Abstract
{

	protected $_name = 't_pessoa';
	protected $_rowClass = 'Pessoa';
        protected $_primary = 'id';

}
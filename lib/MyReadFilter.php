<?php

require_once dirname(__FILE__) . '/PhpSpreadsheet/vendor/autoload.php';

/**
 * MyReadFilter.
 *
 * 
 *
 * @package lib.model
 */

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
	private $_startRow = 0;

	private $_endRow = 0;

	private $_columns = array();

	public function __construct($startRow, $endRow, $columns)
	{
		$this->_startRow	= $startRow;
		$this->_endRow		= $endRow;
		$this->_columns		= $columns;
	}

	public function readCell($column, $row, $worksheetName = ''): bool
	{
		if ($row >= $this->_startRow && $row <= $this->_endRow) {
			if (in_array($column, $this->_columns)) {
				return true;
			}
		}
		return false;
	}
}

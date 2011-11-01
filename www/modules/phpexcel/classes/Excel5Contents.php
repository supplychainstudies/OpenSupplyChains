<?php defined('SYSPATH') or die('No direct access allowed.');

require ('OLEReadContents.php');
class PHPExcel_Reader_Excel5Contents extends PHPExcel_Reader_Excel5
{
	/**
	 * Use OLE reader to extract the relevant data streams from the OLE file
	 *
	 * @param string $pFilename
	 */
	private function _loadOLE($contents)
	{
		// OLE reader
		$ole = new PHPExcel_Shared_OLEReadContents();

		// get excel data,
		$res = $ole->read($contents);
		// Get workbook data: workbook stream + sheet streams
		$this->_data = $ole->getStream($ole->wrkbook);

		// Get summary information data
		$this->_summaryInformation = $ole->getStream($ole->summaryInformation);

		// Get additional document summary information data
		$this->_documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);

		// Get user-defined property data
//		$this->_userDefinedProperties = $ole->getUserDefinedProperties();
	}

	
}

<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * PHP Excel library. Helper class to make spreadsheet creation easier.
 *
 * @package    Spreadsheet
 * @author     Flynsarmy
 * @website    http://www.flynsarmy.com/
 * @license    TEH FREEZ
 */
class Spreadsheet
{
	const VENDOR_PACKAGE = "vendor/phpexcel/PHPExcel/";
	private $_spreadsheet;

	/*
	 * Purpose: Creates the spreadsheet with given or default settings
	 * Input: array $headers with optional parameters: title, subject, description, author
	 * Returns: void
	 */
	public function __construct($headers=array())
	{
		$headers = array_merge(array(
			'title'			=> 'New Spreadsheet',
			'subject'		=> 'New Spreadsheet',
			'description'	=> 'New Spreadsheet',
			'author'		=> 'ClubSuntory',

		), $headers);

		$this->_spreadsheet = new PHPExcel();
		// Set properties
		$this->_spreadsheet->getProperties()
			->setCreator( $headers['author'] )
			->setTitle( $headers['title'] )
			->setSubject( $headers['subject'] )
			->setDescription( $headers['description'] );
			//->setActiveSheetIndex(0);
		//$this->_spreadsheet->getActiveSheet()->setTitle('Minimalistic demo');
	}

	/*
	 * Purpose Writes cells to the spreadsheet
	 * Input: array of array( [row] => array([col]=>[value]) ) ie $arr[row][col] => value
	 * Returns: void
	 */
	public function setData(array $data, $multi_sheet=false)
	{
		if ( empty($this->_spreadsheet) )
			$this->create();

		//Single sheet ones can just dump everything to the current sheet
		if ( !$multi_sheet )
		{
			$Sheet = $this->_spreadsheet->getActiveSheet();
			$this->setSheetData( $data, $Sheet );
		}
		//Hvae to do a little more work with multi-sheet
		else
		{
			foreach ( $data as $sheetName=>$sheetData )
			{
				$Sheet = $this->_spreadsheet->createSheet();
				$Sheet->setTitle( $sheetName );
				$this->setSheetData( $sheetData, $Sheet );
			}
			//Now remove the auto-created blank sheet at start of XLS
			$this->_spreadsheet->removeSheetByIndex( 0 );
		}

		/*
		array(
			1 => array('A1', 'B1', 'C1', 'D1', 'E1')
			2 => array('A2', 'B2', 'C2', 'D2', 'E2')
			3 => array('A3', 'B3', 'C3', 'D3', 'E3')
		);
		*/
	}

	public function setSheetData( array $data, PHPExcel_Worksheet $Sheet )
	{
		foreach ( $data as $row => $columns )
			foreach ( $columns as $column => $value )
				$Sheet->setCellValueByColumnAndRow($column, $row, $value);
	}

	/*
	 * Purpose: Writes spreadsheet to file
	 * Input: array $settings with optional parameters: format, path, name (no extension)
	 * Returns: Path to spreadsheet
	 */
	public function save( $settings=array() )
	{
		if ( empty($this->_spreadsheet) )
			$this->create();

		//Used for saving sheets
		require self::VENDOR_PACKAGE.'IOFactory.php';

		$settings = array_merge(array(
			'format'		=> 'Excel2007',
			'path'			=> APPPATH.'assets/downloads/spreadsheets/',
			'name'			=> 'NewSpreadsheet'

		), $settings);

		//Generate full path
		$settings['fullpath'] = $settings['path'] . $settings['name'] . '_'.time().'.xlsx';

		$Writer = PHPExcel_IOFactory::createWriter($this->_spreadsheet, $settings['format']);
		// If you want to output e.g. a PDF file, simply do:
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		$Writer->save( $settings['fullpath'] );

		return $settings['fullpath'];
	}
}

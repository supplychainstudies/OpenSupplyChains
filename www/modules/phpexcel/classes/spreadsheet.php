<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * PHP Excel library. Helper class to make spreadsheet creation easier.
 *
 * @package    Spreadsheet
 * @author     Flynsarmy, Dmitry Shovchko
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Spreadsheet
{
    private $_spreadsheet;
    private $exts = array(
        'CSV'       => 'csv',
        'PDF'       => 'pdf',
        'Excel5'    => 'xls',
        'Excel2007' => 'xlsx',
    );
    private $mimes = array(
        'CSV'       => 'text/csv',
        'PDF'       => 'application/pdf',
        'Excel5'    => 'application/vnd.ms-excel',
        'Excel2007' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    );

    /**
     * Creates the spreadsheet with given or default settings
     * 
     * @param array $headers with optional parameters: title, subject, description, author
     * @return void
     */
    public function __construct($headers=array())
    {
        $headers = array_merge(array(
            'title'         => 'New Spreadsheet',
            'subject'       => 'New Spreadsheet',
            'description'   => 'New Spreadsheet',
            'author'        => 'ClubSuntory',
        ), $headers);
        
        $this->_spreadsheet = new PHPExcel();
        // Set properties
        $this->_spreadsheet->getProperties()
            ->setCreator( $headers['author'] )
            ->setTitle( $headers['title'] )
            ->setSubject( $headers['subject'] )
            ->setDescription( $headers['description'] );
    }
    
    /**
     * Set active sheet index
     * 
     * @param int $index Active sheet index
     * @return void
     */
    public function set_active_sheet($index)
    {
        $this->_spreadsheet->setActiveSheetIndex($index);
    }

    /**
     * Set active sheet index
     * 
     * @param int $index Active sheet index
     * @return void
     */
    public function create_active_sheet()
    {
		$this->_spreadsheet->setActiveSheetIndex($this->_spreadsheet->getIndex($this->_spreadsheet->createSheet()));
    }
    
    /**
     * Get the currently active sheet
     * 
     * @return PHPExcel_Worksheet
     */
    public function get_active_sheet()
    {
        return $this->_spreadsheet->getActiveSheet();
    }

    /**
     * Writes cells to the spreadsheet
     *  array(
     *     1 => array('A1', 'B1', 'C1', 'D1', 'E1'),
     *     2 => array('A2', 'B2', 'C2', 'D2', 'E2'),
     *     3 => array('A3', 'B3', 'C3', 'D3', 'E3'),
     *  );
     * 
     * @param array of array( [row] => array([col]=>[value]) ) ie $arr[row][col] => value
     * @return void
     */
    public function set_data(array $data, $multi_sheet=false)
    {
        //Single sheet ones can just dump everything to the current sheet
        if ( !$multi_sheet )
        {
            $sheet = $this->_spreadsheet->getActiveSheet();
            $this->set_sheet_data($data, $sheet);
        }
        //Have to do a little more work with multi-sheet
        else
        {
            foreach ($data as $sheetname=>$sheetData)
            {
                $sheet = $this->_spreadsheet->createSheet();
                $sheet->setTitle($sheetname);
                $this->set_sheet_data($sheetData, $sheet);
            }
            //Now remove the auto-created blank sheet at start of XLS
            $this->_spreadsheet->removeSheetByIndex(0);
        }
    }

    protected function set_sheet_data(array $data, PHPExcel_Worksheet $sheet)
    {
        foreach ($data as $row =>$columns)
            foreach ($columns as $column=>$value)
                $sheet->setCellValueByColumnAndRow($column, $row, $value);
    }

	public function set_column_validation($col, $formula, $type, $prompttitle ="", $promptmessage="",$errortitle ="", $errormessage="") {
		//We cant set the validation for a whole column (as far as I know. if you know better, reimplement this) so, we'll iterate through the rows till $depth 
		$depth = 300;
		$i = 1;
		$objValidation = $this->_spreadsheet->getActiveSheet()->getCell($col.$i)
		->getDataValidation();
		switch ($type) {
			case "DECIMAL":
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_DECIMAL );
			break;
			case "LIST":
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
			break;
			case "CUSTOM":
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_CUSTOM );
			break;			
			case "WHOLE":
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
			break;
		}
		$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
		$objValidation->setAllowBlank(true);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowErrorMessage(true);
		if ($type == "LIST")
			$objValidation->setShowDropDown(true);
		$objValidation->setErrorTitle($errortitle);
		$objValidation->setError($errormessage);
		$objValidation->setPromptTitle($prompttitle);
		$objValidation->setPrompt($promptmessage);
		while ($i < $depth) {
			if (is_array($formula) == false) {
				$use_formula = str_replace("cell", $col.$i, $formula);
				$objValidation->setFormula1($use_formula);
			} else {
				$objValidation->setFormula1($formula[0]);
				$objValidation->setFormula2($formula[1]);
			}
			$this->_spreadsheet->getActiveSheet()->getCell($col.$i)
			->setDataValidation(clone $objValidation);
			$i++;		
		}		
	}
		
	public function protectCells($cells) {
		$this->_spreadsheet->getActiveSheet()->protectCells($cells);
	}
	
	public function freezeTopRow() {
		$this->_spreadsheet->getActiveSheet()->freezePane('A2');
	}
	
	public function set_column_formula($col, $formula) {
		//We cant set the validation for a whole column (as far as I know. if you know better, reimplement this) so, we'll iterate through the rows till $depth 
		$depth = 300;
		$i = 2;
		//str_replace("counter", $i, $formula)
		while ($i < $depth) {
			$this->_spreadsheet->getActiveSheet()->setCellValue($col.$i,"=IF(A2=='beep', TRUE, FALSE)");
			//$this->_spreadsheet->getActiveSheet()->getCell($col.$i)->getCalculatedValue();
			$i++;		
		}		
	}
    
    /**
     * Writes spreadsheet to file
     * 
     * @param array $settings with optional parameters: format, path, name (no extension)
     * @return Path to spreadsheet
     */
    public function save($settings=array())
    {
        $settings = array_merge(array(
            'format'        => 'Excel2007',
            'path'          => APPPATH.'assets/downloads/spreadsheets/',
            'name'          => 'NewSpreadsheet',
        ), $settings);
        
        //Generate full path
        $settings['fullpath'] = $settings['path'].$settings['name'].'_'.time().'.'.$this->exts[$settings['format']];
        
        $writer = PHPExcel_IOFactory::createWriter($this->_spreadsheet, $settings['format']);

        if ($settings['format'] == 'CSV')
        {
            $writer->setUseBOM(true);
        }
        $writer->save($settings['fullpath']);

        return $settings['fullpath'];
    }
    
    /**
     * Send spreadsheet to browser
     * 
     * @param array $settings with optional parameters: format, name (no extension)
     * @return void
     */
    public function send($settings=array())
    {
        $settings = array_merge(array(
            'format'        => 'Excel2007',
            'name'          => 'NewSpreadsheet',
        ), $settings);
        
        $writer = PHPExcel_IOFactory::createWriter($this->_spreadsheet, $settings['format']);
        
        $ext = $this->exts[$settings['format']];
        $mime = $this->mimes[$settings['format']];
        
        $request = Request::instance();
        $request->headers['Content-Type'] = $mime;
        $request->headers['Content-Disposition'] = 'attachment;filename="'.$settings['name'].'.'.$ext.'"';
        $request->headers['Cache-Control'] = 'max-age=0';
        $request->send_headers();
        
        if ($settings['format'] == 'CSV')
        {
            $writer->setUseBOM(true);
        }
        
        $writer->save('php://output');
        exit;
    }
    
}

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

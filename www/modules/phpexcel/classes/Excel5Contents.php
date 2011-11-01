<?php defined('SYSPATH') or die('No direct access allowed.');

require ('OLEReadContents.php');
class PHPExcel_Reader_Excel5Contents extends PHPExcel_Reader_Excel5
{
	/**
	 *	Read data only?
	 *	Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
	 *		or whether it should read both data and formatting
	 *
	 *	@var	boolean
	 */
	private $_readDataOnly = false;

	/**
	 *	Restrict which sheets should be loaded?
	 *	This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
	 *
	 *	@var	array of string
	 */
	private $_loadSheetsOnly = null;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;

	/**
	 * Summary Information stream data.
	 *
	 * @var string
	 */
	private $_summaryInformation;

	/**
	 * Extended Summary Information stream data.
	 *
	 * @var string
	 */
	private $_documentSummaryInformation;

	/**
	 * User-Defined Properties stream data.
	 *
	 * @var string
	 */
	private $_userDefinedProperties;

	/**
	 * Workbook stream data. (Includes workbook globals substream as well as sheet substreams)
	 *
	 * @var string
	 */
	private $_data;

	/**
	 * Size in bytes of $this->_data
	 *
	 * @var int
	 */
	private $_dataSize;

	/**
	 * Current position in stream
	 *
	 * @var integer
	 */
	private $_pos;

	/**
	 * Workbook to be returned by the reader.
	 *
	 * @var PHPExcel
	 */
	private $_phpExcel;

	/**
	 * Worksheet that is currently being built by the reader.
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_phpSheet;

	/**
	 * BIFF version
	 *
	 * @var int
	 */
	private $_version;

	/**
	 * Codepage set in the Excel file being read. Only important for BIFF5 (Excel 5.0 - Excel 95)
	 * For BIFF8 (Excel 97 - Excel 2003) this will always have the value 'UTF-16LE'
	 *
	 * @var string
	 */
	private $_codepage;

	/**
	 * Shared formats
	 *
	 * @var array
	 */
	private $_formats;

	/**
	 * Shared fonts
	 *
	 * @var array
	 */
	private $_objFonts;

	/**
	 * Color palette
	 *
	 * @var array
	 */
	private $_palette;

	/**
	 * Worksheets
	 *
	 * @var array
	 */
	private $_sheets;

	/**
	 * External books
	 *
	 * @var array
	 */
	private $_externalBooks;

	/**
	 * REF structures. Only applies to BIFF8.
	 *
	 * @var array
	 */
	private $_ref;

	/**
	 * External names
	 *
	 * @var array
	 */
	private $_externalNames;

	/**
	 * Defined names
	 *
	 * @var array
	 */
	private $_definedname;

	/**
	 * Shared strings. Only applies to BIFF8.
	 *
	 * @var array
	 */
	private $_sst;

	/**
	 * Panes are frozen? (in sheet currently being read). See WINDOW2 record.
	 *
	 * @var boolean
	 */
	private $_frozen;

	/**
	 * Fit printout to number of pages? (in sheet currently being read). See SHEETPR record.
	 *
	 * @var boolean
	 */
	private $_isFitToPages;

	/**
	 * Objects. One OBJ record contributes with one entry.
	 *
	 * @var array
	 */
	private $_objs;

	/**
	 * Text Objects. One TXO record corresponds with one entry.
	 *
	 * @var array
	 */
	private $_textObjects;

	/**
	 * Cell Annotations (BIFF8)
	 *
	 * @var array
	 */
	private $_cellNotes;

	/**
	 * The combined MSODRAWINGGROUP data
	 *
	 * @var string
	 */
	private $_drawingGroupData;

	/**
	 * The combined MSODRAWING data (per sheet)
	 *
	 * @var string
	 */
	private $_drawingData;

	/**
	 * Keep track of XF index
	 *
	 * @var int
	 */
	private $_xfIndex;

	/**
	 * Mapping of XF index (that is a cell XF) to final index in cellXf collection
	 *
	 * @var array
	 */
	private $_mapCellXfIndex;

	/**
	 * Mapping of XF index (that is a style XF) to final index in cellStyleXf collection
	 *
	 * @var array
	 */
	private $_mapCellStyleXfIndex;

	/**
	 * The shared formulas in a sheet. One SHAREDFMLA record contributes with one value.
	 *
	 * @var array
	 */
	private $_sharedFormulas;

	/**
	 * The shared formula parts in a sheet. One FORMULA record contributes with one value if it
	 * refers to a shared formula.
	 *
	 * @var array
	 */
	private $_sharedFormulaParts;
	
	
	
		/**
		 * Loads PHPExcel from file
		 *
		 * @param 	string 		$pFilename
		 * @return 	PHPExcel
		 * @throws 	Exception
		 */
		public function loadContents($contents)
		{
			// Read the OLE file
			$this->_loadOLEContents($contents);

			// Initialisations
			$this->_phpExcel = new PHPExcel;
			$this->_phpExcel->removeSheetByIndex(0); // remove 1st sheet
			if (!$this->_readDataOnly) {
				$this->_phpExcel->removeCellStyleXfByIndex(0); // remove the default style
				$this->_phpExcel->removeCellXfByIndex(0); // remove the default style
			}

			// Read the summary information stream (containing meta data)
			$this->_readSummaryInformation();

			// Read the Additional document summary information stream (containing application-specific meta data)
			$this->_readDocumentSummaryInformation();

			// total byte size of Excel data (workbook global substream + sheet substreams)
			$this->_dataSize = strlen($this->_data);

			// initialize
			$this->_pos					= 0;
			$this->_codepage			= 'CP1252';
			$this->_formats				= array();
			$this->_objFonts			= array();
			$this->_palette				= array();
			$this->_sheets				= array();
			$this->_externalBooks		= array();
			$this->_ref					= array();
			$this->_definedname			= array();
			$this->_sst					= array();
			$this->_drawingGroupData	= '';
			$this->_xfIndex				= '';
			$this->_mapCellXfIndex		= array();
			$this->_mapCellStyleXfIndex	= array();

			// Parse Workbook Global Substream
			while ($this->_pos < $this->_dataSize) {
				$code = self::_GetInt2d($this->_data, $this->_pos);

				switch ($code) {
					case self::XLS_Type_BOF:			$this->_readBof();				break;
					case self::XLS_Type_FILEPASS:		$this->_readFilepass();			break;
					case self::XLS_Type_CODEPAGE:		$this->_readCodepage();			break;
					case self::XLS_Type_DATEMODE:		$this->_readDateMode();			break;
					case self::XLS_Type_FONT:			$this->_readFont();				break;
					case self::XLS_Type_FORMAT:			$this->_readFormat();			break;
					case self::XLS_Type_XF:				$this->_readXf();				break;
					case self::XLS_Type_XFEXT:			$this->_readXfExt();			break;
					case self::XLS_Type_STYLE:			$this->_readStyle();			break;
					case self::XLS_Type_PALETTE:		$this->_readPalette();			break;
					case self::XLS_Type_SHEET:			$this->_readSheet();			break;
					case self::XLS_Type_EXTERNALBOOK:	$this->_readExternalBook();		break;
					case self::XLS_Type_EXTERNNAME:		$this->_readExternName();		break;
					case self::XLS_Type_EXTERNSHEET:	$this->_readExternSheet();		break;
					case self::XLS_Type_DEFINEDNAME:	$this->_readDefinedName();		break;
					case self::XLS_Type_MSODRAWINGGROUP:	$this->_readMsoDrawingGroup();	break;
					case self::XLS_Type_SST:			$this->_readSst();				break;
					case self::XLS_Type_EOF:			$this->_readDefault();			break 2;
					default:							$this->_readDefault();			break;
				}
			}

			// Resolve indexed colors for font, fill, and border colors
			// Cannot be resolved already in XF record, because PALETTE record comes afterwards
			if (!$this->_readDataOnly) {
				foreach ($this->_objFonts as $objFont) {
					if (isset($objFont->colorIndex)) {
						$color = self::_readColor($objFont->colorIndex,$this->_palette,$this->_version);
						$objFont->getColor()->setRGB($color['rgb']);
					}
				}

				foreach ($this->_phpExcel->getCellXfCollection() as $objStyle) {
					// fill start and end color
					$fill = $objStyle->getFill();

					if (isset($fill->startcolorIndex)) {
						$startColor = self::_readColor($fill->startcolorIndex,$this->_palette,$this->_version);
						$fill->getStartColor()->setRGB($startColor['rgb']);
					}

					if (isset($fill->endcolorIndex)) {
						$endColor = self::_readColor($fill->endcolorIndex,$this->_palette,$this->_version);
						$fill->getEndColor()->setRGB($endColor['rgb']);
					}

					// border colors
					$top      = $objStyle->getBorders()->getTop();
					$right    = $objStyle->getBorders()->getRight();
					$bottom   = $objStyle->getBorders()->getBottom();
					$left     = $objStyle->getBorders()->getLeft();
					$diagonal = $objStyle->getBorders()->getDiagonal();

					if (isset($top->colorIndex)) {
						$borderTopColor = self::_readColor($top->colorIndex,$this->_palette,$this->_version);
						$top->getColor()->setRGB($borderTopColor['rgb']);
					}

					if (isset($right->colorIndex)) {
						$borderRightColor = self::_readColor($right->colorIndex,$this->_palette,$this->_version);
						$right->getColor()->setRGB($borderRightColor['rgb']);
					}

					if (isset($bottom->colorIndex)) {
						$borderBottomColor = self::_readColor($bottom->colorIndex,$this->_palette,$this->_version);
						$bottom->getColor()->setRGB($borderBottomColor['rgb']);
					}

					if (isset($left->colorIndex)) {
						$borderLeftColor = self::_readColor($left->colorIndex,$this->_palette,$this->_version);
						$left->getColor()->setRGB($borderLeftColor['rgb']);
					}

					if (isset($diagonal->colorIndex)) {
						$borderDiagonalColor = self::_readColor($diagonal->colorIndex,$this->_palette,$this->_version);
						$diagonal->getColor()->setRGB($borderDiagonalColor['rgb']);
					}
				}
			}

			// treat MSODRAWINGGROUP records, workbook-level Escher
			if (!$this->_readDataOnly && $this->_drawingGroupData) {
				$escherWorkbook = new PHPExcel_Shared_Escher();
				$reader = new PHPExcel_Reader_Excel5_Escher($escherWorkbook);
				$escherWorkbook = $reader->load($this->_drawingGroupData);

				// debug Escher stream
				//$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
				//$debug->load($this->_drawingGroupData);
			}

			// Parse the individual sheets
			foreach ($this->_sheets as $sheet) {

				if ($sheet['sheetType'] != 0x00) {
					// 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
					continue;
				}

				// check if sheet should be skipped
				if (isset($this->_loadSheetsOnly) && !in_array($sheet['name'], $this->_loadSheetsOnly)) {
					continue;
				}

				// add sheet to PHPExcel object
				$this->_phpSheet = $this->_phpExcel->createSheet();
				$this->_phpSheet->setTitle($sheet['name']);
				$this->_phpSheet->setSheetState($sheet['sheetState']);

				$this->_pos = $sheet['offset'];

				// Initialize isFitToPages. May change after reading SHEETPR record.
				$this->_isFitToPages = false;

				// Initialize drawingData
				$this->_drawingData = '';

				// Initialize objs
				$this->_objs = array();

				// Initialize shared formula parts
				$this->_sharedFormulaParts = array();

				// Initialize shared formulas
				$this->_sharedFormulas = array();

				// Initialize text objs
				$this->_textObjects = array();

				// Initialize cell annotations
				$this->_cellNotes = array();
				$this->textObjRef = -1;

				while ($this->_pos <= $this->_dataSize - 4) {
					$code = self::_GetInt2d($this->_data, $this->_pos);

					switch ($code) {
						case self::XLS_Type_BOF:					$this->_readBof();						break;
						case self::XLS_Type_PRINTGRIDLINES:			$this->_readPrintGridlines();			break;
						case self::XLS_Type_DEFAULTROWHEIGHT:		$this->_readDefaultRowHeight();			break;
						case self::XLS_Type_SHEETPR:				$this->_readSheetPr();					break;
						case self::XLS_Type_HORIZONTALPAGEBREAKS:	$this->_readHorizontalPageBreaks();		break;
						case self::XLS_Type_VERTICALPAGEBREAKS:		$this->_readVerticalPageBreaks();		break;
						case self::XLS_Type_HEADER:					$this->_readHeader();					break;
						case self::XLS_Type_FOOTER:					$this->_readFooter();					break;
						case self::XLS_Type_HCENTER:				$this->_readHcenter();					break;
						case self::XLS_Type_VCENTER:				$this->_readVcenter();					break;
						case self::XLS_Type_LEFTMARGIN:				$this->_readLeftMargin();				break;
						case self::XLS_Type_RIGHTMARGIN:			$this->_readRightMargin();				break;
						case self::XLS_Type_TOPMARGIN:				$this->_readTopMargin();				break;
						case self::XLS_Type_BOTTOMMARGIN:			$this->_readBottomMargin();				break;
						case self::XLS_Type_PAGESETUP:				$this->_readPageSetup();				break;
						case self::XLS_Type_PROTECT:				$this->_readProtect();					break;
						case self::XLS_Type_SCENPROTECT:			$this->_readScenProtect();				break;
						case self::XLS_Type_OBJECTPROTECT:			$this->_readObjectProtect();			break;
						case self::XLS_Type_PASSWORD:				$this->_readPassword();					break;
						case self::XLS_Type_DEFCOLWIDTH:			$this->_readDefColWidth();				break;
						case self::XLS_Type_COLINFO:				$this->_readColInfo();					break;
						case self::XLS_Type_DIMENSION:				$this->_readDefault();					break;
						case self::XLS_Type_ROW:					$this->_readRow();						break;
						case self::XLS_Type_DBCELL:					$this->_readDefault();					break;
						case self::XLS_Type_RK:						$this->_readRk();						break;
						case self::XLS_Type_LABELSST:				$this->_readLabelSst();					break;
						case self::XLS_Type_MULRK:					$this->_readMulRk();					break;
						case self::XLS_Type_NUMBER:					$this->_readNumber();					break;
						case self::XLS_Type_FORMULA:				$this->_readFormula();					break;
						case self::XLS_Type_SHAREDFMLA:				$this->_readSharedFmla();				break;
						case self::XLS_Type_BOOLERR:				$this->_readBoolErr();					break;
						case self::XLS_Type_MULBLANK:				$this->_readMulBlank();					break;
						case self::XLS_Type_LABEL:					$this->_readLabel();					break;
						case self::XLS_Type_BLANK:					$this->_readBlank();					break;
						case self::XLS_Type_MSODRAWING:				$this->_readMsoDrawing();				break;
						case self::XLS_Type_OBJ:					$this->_readObj();						break;
						case self::XLS_Type_WINDOW2:				$this->_readWindow2();					break;
						case self::XLS_Type_SCL:					$this->_readScl();						break;
						case self::XLS_Type_PANE:					$this->_readPane();						break;
						case self::XLS_Type_SELECTION:				$this->_readSelection();				break;
						case self::XLS_Type_MERGEDCELLS:			$this->_readMergedCells();				break;
						case self::XLS_Type_HYPERLINK:				$this->_readHyperLink();				break;
						case self::XLS_Type_DATAVALIDATIONS:		$this->_readDataValidations();			break;
						case self::XLS_Type_DATAVALIDATION:			$this->_readDataValidation();			break;
						case self::XLS_Type_SHEETLAYOUT:			$this->_readSheetLayout();				break;
						case self::XLS_Type_SHEETPROTECTION:		$this->_readSheetProtection();			break;
						case self::XLS_Type_RANGEPROTECTION:		$this->_readRangeProtection();			break;
						case self::XLS_Type_NOTE:					$this->_readNote();						break;
						//case self::XLS_Type_IMDATA:				$this->_readImData();					break;
						case self::XLS_Type_TXO:					$this->_readTextObject();				break;
						case self::XLS_Type_CONTINUE:				$this->_readContinue();					break;
						case self::XLS_Type_EOF:					$this->_readDefault();					break 2;
						default:									$this->_readDefault();					break;
					}

				}

				// treat MSODRAWING records, sheet-level Escher
				if (!$this->_readDataOnly && $this->_drawingData) {
					$escherWorksheet = new PHPExcel_Shared_Escher();
					$reader = new PHPExcel_Reader_Excel5_Escher($escherWorksheet);
					$escherWorksheet = $reader->load($this->_drawingData);

					// debug Escher stream
					//$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
					//$debug->load($this->_drawingData);

					// get all spContainers in one long array, so they can be mapped to OBJ records
					$allSpContainers = $escherWorksheet->getDgContainer()->getSpgrContainer()->getAllSpContainers();
				}

				// treat OBJ records
				foreach ($this->_objs as $n => $obj) {
	//				echo '<hr /><b>Object</b> reference is ',$n,'<br />';
	//				var_dump($obj);
	//				echo '<br />';

					// the first shape container never has a corresponding OBJ record, hence $n + 1
					$spContainer = $allSpContainers[$n + 1];

					// we skip all spContainers that are a part of a group shape since we cannot yet handle those
					if ($spContainer->getNestingLevel() > 1) {
						continue;
					}

					// calculate the width and height of the shape
					list($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($spContainer->getStartCoordinates());
					list($endColumn, $endRow) = PHPExcel_Cell::coordinateFromString($spContainer->getEndCoordinates());

					$startOffsetX = $spContainer->getStartOffsetX();
					$startOffsetY = $spContainer->getStartOffsetY();
					$endOffsetX = $spContainer->getEndOffsetX();
					$endOffsetY = $spContainer->getEndOffsetY();

					$width = PHPExcel_Shared_Excel5::getDistanceX($this->_phpSheet, $startColumn, $startOffsetX, $endColumn, $endOffsetX);
					$height = PHPExcel_Shared_Excel5::getDistanceY($this->_phpSheet, $startRow, $startOffsetY, $endRow, $endOffsetY);

					// calculate offsetX and offsetY of the shape
					$offsetX = $startOffsetX * PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, $startColumn) / 1024;
					$offsetY = $startOffsetY * PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $startRow) / 256;

					switch ($obj['otObjType']) {

					case 0x19:
						// Note
	//					echo 'Cell Annotation Object<br />';
	//					echo 'Object ID is ',$obj['idObjID'],'<br />';
	//
						if (isset($this->_cellNotes[$obj['idObjID']])) {
							$cellNote = $this->_cellNotes[$obj['idObjID']];

	//						echo '_cellNotes[',$obj['idObjID'],']: ';
	//						var_dump($cellNote);
	//						echo '<br />';
	//
							if (isset($this->_textObjects[$obj['idObjID']])) {
								$textObject = $this->_textObjects[$obj['idObjID']];
	//							echo '_textObject: ';
	//							var_dump($textObject);
	//							echo '<br />';
	//
								$this->_cellNotes[$obj['idObjID']]['objTextData'] = $textObject;
								$text = $textObject['text'];
							}
	//						echo $text,'<br />';
						}
						break;

					case 0x08:
	//					echo 'Picture Object<br />';
						// picture

						// get index to BSE entry (1-based)
						$BSEindex = $spContainer->getOPT(0x0104);
						$BSECollection = $escherWorkbook->getDggContainer()->getBstoreContainer()->getBSECollection();
						$BSE = $BSECollection[$BSEindex - 1];
						$blipType = $BSE->getBlipType();

						// need check because some blip types are not supported by Escher reader such as EMF
						if ($blip = $BSE->getBlip()) {
							$ih = imagecreatefromstring($blip->getData());
							$drawing = new PHPExcel_Worksheet_MemoryDrawing();
							$drawing->setImageResource($ih);

							// width, height, offsetX, offsetY
							$drawing->setResizeProportional(false);
							$drawing->setWidth($width);
							$drawing->setHeight($height);
							$drawing->setOffsetX($offsetX);
							$drawing->setOffsetY($offsetY);

							switch ($blipType) {
								case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
									$drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
									$drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG);
									break;

								case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
									$drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
									$drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
									break;
							}

							$drawing->setWorksheet($this->_phpSheet);
							$drawing->setCoordinates($spContainer->getStartCoordinates());
						}

						break;

					default:
						// other object type
						break;

					}
				}

				// treat SHAREDFMLA records
				if ($this->_version == self::XLS_BIFF8) {
					foreach ($this->_sharedFormulaParts as $cell => $baseCell) {
						list($column, $row) = PHPExcel_Cell::coordinateFromString($cell);
						if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($column, $row, $this->_phpSheet->getTitle()) ) {
							$formula = $this->_getFormulaFromStructure($this->_sharedFormulas[$baseCell], $cell);
							$this->_phpSheet->getCell($cell)->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
						}
					}
				}

				if (count($this->_cellNotes) > 0) {
					foreach($this->_cellNotes as $note => $noteDetails) {
	//					echo '<b>Cell annotation ',$note,'</b><br />';
	//					var_dump($noteDetails);
	//					echo '<br />';
						$cellAddress = str_replace('$','',$noteDetails['cellRef']);
						$this->_phpSheet->getComment( $cellAddress )
														->setAuthor( $noteDetails['author'] )
														->setText($this->_parseRichText($noteDetails['objTextData']['text']) );
					}
				}
			}

			// add the named ranges (defined names)
			foreach ($this->_definedname as $definedName) {
				if ($definedName['isBuiltInName']) {
					switch ($definedName['name']) {

					case pack('C', 0x06):
						// print area
						//	in general, formula looks like this: Foo!$C$7:$J$66,Bar!$A$1:$IV$2

						$ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

						$extractedRanges = array();
						foreach ($ranges as $range) {
							// $range should look like one of these
							//		Foo!$C$7:$J$66
							//		Bar!$A$1:$IV$2

							$explodes = explode('!', $range);	// FIXME: what if sheetname contains exclamation mark?
							$sheetName = $explodes[0];

							if (count($explodes) == 2) {
								$extractedRanges[] = str_replace('$', '', $explodes[1]); // C7:J66
							}
						}
						if ($docSheet = $this->_phpExcel->getSheetByName($sheetName)) {
							$docSheet->getPageSetup()->setPrintArea(implode(',', $extractedRanges)); // C7:J66,A1:IV2
						}
						break;

					case pack('C', 0x07):
						// print titles (repeating rows)
						// Assuming BIFF8, there are 3 cases
						// 1. repeating rows
						//		formula looks like this: Sheet!$A$1:$IV$2
						//		rows 1-2 repeat
						// 2. repeating columns
						//		formula looks like this: Sheet!$A$1:$B$65536
						//		columns A-B repeat
						// 3. both repeating rows and repeating columns
						//		formula looks like this: Sheet!$A$1:$B$65536,Sheet!$A$1:$IV$2

						$ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

						foreach ($ranges as $range) {
							// $range should look like this one of these
							//		Sheet!$A$1:$B$65536
							//		Sheet!$A$1:$IV$2

							$explodes = explode('!', $range);

							if (count($explodes) == 2) {
								if ($docSheet = $this->_phpExcel->getSheetByName($explodes[0])) {

									$extractedRange = $explodes[1];
									$extractedRange = str_replace('$', '', $extractedRange);

									$coordinateStrings = explode(':', $extractedRange);
									if (count($coordinateStrings) == 2) {
										list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[0]);
										list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[1]);

										if ($firstColumn == 'A' and $lastColumn == 'IV') {
											// then we have repeating rows
											$docSheet->getPageSetup()->setRowsToRepeatAtTop(array($firstRow, $lastRow));
										} elseif ($firstRow == 1 and $lastRow == 65536) {
											// then we have repeating columns
											$docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($firstColumn, $lastColumn));
										}
									}
								}
							}
						}
						break;

					}
				} else {
					// Extract range
					$explodes = explode('!', $definedName['formula']);

					if (count($explodes) == 2) {
						if ($docSheet = $this->_phpExcel->getSheetByName($explodes[0])) {
							$extractedRange = $explodes[1];
							$extractedRange = str_replace('$', '', $extractedRange);

							$localOnly = ($definedName['scope'] == 0) ? false : true;
							$scope = ($definedName['scope'] == 0) ?
								null : $this->_phpExcel->getSheetByName($this->_sheets[$definedName['scope'] - 1]['name']);

							$this->_phpExcel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $docSheet, $extractedRange, $localOnly, $scope) );
						}
					}
				}
			}

			return $this->_phpExcel;
		}
	
	
	
	
	/**
	 * Use OLE reader to extract the relevant data streams from the OLE file
	 *
	 * @param string $pFilename
	 */
	private function _loadOLEContents($contents)
	{
		// OLE reader
		$ole = new PHPExcel_Shared_OLEReadContents();

		// get excel data,
		$res = $ole->readContents($contents);
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

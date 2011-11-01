<?php defined('SYSPATH') or die('No direct access allowed.');

class PHPExcel_Shared_OLEReadContents extends PHPExcel_Shared_OLERead
{
	
    /**
	 * takes direct contents of a file
	 *
	 * @param $contents
	 * @throws Exception
	 */
	public function readContents($contents)
	{
		$this->data = $contents;

		// Check OLE identifier
		if (substr($this->data, 0, 8) != self::IDENTIFIER_OLE) {
			throw new Exception('The file is not recognised as an OLE file');
		}

		// Total number of sectors used for the SAT
		$this->numBigBlockDepotBlocks = self::_GetInt4d($this->data, self::NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);

		// SecID of the first sector of the directory stream
		$this->rootStartBlock = self::_GetInt4d($this->data, self::ROOT_START_BLOCK_POS);

		// SecID of the first sector of the SSAT (or -2 if not extant)
		$this->sbdStartBlock = self::_GetInt4d($this->data, self::SMALL_BLOCK_DEPOT_BLOCK_POS);

		// SecID of the first sector of the MSAT (or -2 if no additional sectors are used)
		$this->extensionBlock = self::_GetInt4d($this->data, self::EXTENSION_BLOCK_POS);

		// Total number of sectors used by MSAT
		$this->numExtensionBlocks = self::_GetInt4d($this->data, self::NUM_EXTENSION_BLOCK_POS);

		$bigBlockDepotBlocks = array();
		$pos = self::BIG_BLOCK_DEPOT_BLOCKS_POS;

		$bbdBlocks = $this->numBigBlockDepotBlocks;

		if ($this->numExtensionBlocks != 0) {
			$bbdBlocks = (self::BIG_BLOCK_SIZE - self::BIG_BLOCK_DEPOT_BLOCKS_POS)/4;
		}

		for ($i = 0; $i < $bbdBlocks; ++$i) {
			  $bigBlockDepotBlocks[$i] = self::_GetInt4d($this->data, $pos);
			  $pos += 4;
		}

		for ($j = 0; $j < $this->numExtensionBlocks; ++$j) {
			$pos = ($this->extensionBlock + 1) * self::BIG_BLOCK_SIZE;
			$blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, self::BIG_BLOCK_SIZE / 4 - 1);

			for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; ++$i) {
				$bigBlockDepotBlocks[$i] = self::_GetInt4d($this->data, $pos);
				$pos += 4;
			}

			$bbdBlocks += $blocksToRead;
			if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
				$this->extensionBlock = self::_GetInt4d($this->data, $pos);
			}
		}

		$pos = $index = 0;
		$this->bigBlockChain = array();

		$bbs = self::BIG_BLOCK_SIZE / 4;
		for ($i = 0; $i < $this->numBigBlockDepotBlocks; ++$i) {
			$pos = ($bigBlockDepotBlocks[$i] + 1) * self::BIG_BLOCK_SIZE;

			for ($j = 0 ; $j < $bbs; ++$j) {
				$this->bigBlockChain[$index] = self::_GetInt4d($this->data, $pos);
				$pos += 4 ;
				++$index;
			}
		}

		$pos = $index = 0;
		$sbdBlock = $this->sbdStartBlock;
		$this->smallBlockChain = array();

		while ($sbdBlock != -2) {
			$pos = ($sbdBlock + 1) * self::BIG_BLOCK_SIZE;

			for ($j = 0; $j < $bbs; ++$j) {
				$this->smallBlockChain[$index] = self::_GetInt4d($this->data, $pos);
				$pos += 4;
				++$index;
			}

			$sbdBlock = $this->bigBlockChain[$sbdBlock];
		}

		// read the directory stream
		$block = $this->rootStartBlock;
		$this->entry = $this->_readData($block);

		$this->_readPropertySets();
	}
	
	/**
	 * Read 4 bytes of data at specified position
	 *
	 * @param string $data
	 * @param int $pos
	 * @return int
	 */
	public static function _GetInt4d($data, $pos)
	{
		// FIX: represent numbers correctly on 64-bit system
		// http://sourceforge.net/tracker/index.php?func=detail&aid=1487372&group_id=99160&atid=623334
		// Hacked by Andreas Rehm 2006 to ensure correct result of the <<24 block on 32 and 64bit systems
		$_or_24 = ord($data[$pos + 3]);
		if ($_or_24 >= 128) {
			// negative number
			$_ord_24 = -abs((256 - $_or_24) << 24);
		} else {
			$_ord_24 = ($_or_24 & 127) << 24;
		}
		return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | $_ord_24;
	}
	
	
	/**
	 * Read a standard stream (by joining sectors using information from SAT)
	 *
	 * @param int $bl Sector ID where the stream starts
	 * @return string Data for standard stream
	 */
	public function _readData($bl)
	{
		$block = $bl;
		$data = '';

		while ($block != -2)  {
			$pos = ($block + 1) * self::BIG_BLOCK_SIZE;
			$data .= substr($this->data, $pos, self::BIG_BLOCK_SIZE);
			$block = $this->bigBlockChain[$block];
		}
		return $data;
	 }
	
		/**
		 * Read entries in the directory stream.
		 */
		public function _readPropertySets() {
			$offset = 0;

			// loop through entires, each entry is 128 bytes
			$entryLen = strlen($this->entry);
			while ($offset < $entryLen) {
				// entry data (128 bytes)
				$d = substr($this->entry, $offset, self::PROPERTY_STORAGE_BLOCK_SIZE);

				// size in bytes of name
				$nameSize = ord($d[self::SIZE_OF_NAME_POS]) | (ord($d[self::SIZE_OF_NAME_POS+1]) << 8);

				// type of entry
				$type = ord($d[self::TYPE_POS]);

				// sectorID of first sector or short sector, if this entry refers to a stream (the case with workbook)
				// sectorID of first sector of the short-stream container stream, if this entry is root entry
				$startBlock = self::_GetInt4d($d, self::START_BLOCK_POS);

				$size = self::_GetInt4d($d, self::SIZE_POS);

				$name = str_replace("\x00", "", substr($d,0,$nameSize));

				$this->props[] = array (
					'name' => $name,
					'type' => $type,
					'startBlock' => $startBlock,
					'size' => $size);

				// Workbook directory entry (BIFF5 uses Book, BIFF8 uses Workbook)
				if (($name == 'Workbook') || ($name == 'Book') || ($name == 'WORKBOOK') || ($name == 'BOOK')) {
					$this->wrkbook = count($this->props) - 1;
				}

				// Root entry
				if ($name == 'Root Entry' || $name == 'ROOT ENTRY' || $name == 'R') {
					$this->rootentry = count($this->props) - 1;
				}

				// Summary information
				if ($name == chr(5) . 'SummaryInformation') {
	//				echo 'Summary Information<br />';
					$this->summaryInformation = count($this->props) - 1;
				}

				// Additional Document Summary information
				if ($name == chr(5) . 'DocumentSummaryInformation') {
	//				echo 'Document Summary Information<br />';
					$this->documentSummaryInformation = count($this->props) - 1;
				}

				$offset += self::PROPERTY_STORAGE_BLOCK_SIZE;
			}

		}
	
	
}
<?php defined('SYSPATH') or die('No direct access allowed.');

class PHPExcel_Shared_OLEReadContents extends PHPExcel_Shared_OLERead
{
    /**
	 * takes direct contents of a file
	 *
	 * @param $contents
	 * @throws Exception
	 */
	public function read($contents)
	{
		$this->data = $contents;

		// Check OLE identifier
		if (substr($this->data, 0, 8) != self::IDENTIFIER_OLE) {
			throw new Exception('The filename ' . $sFileName . ' is not recognised as an OLE file');
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
	
}
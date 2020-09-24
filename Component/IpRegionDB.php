<?php

namespace Leon\BswBundle\Component;

use Exception;

defined('INDEX_BLOCK_LENGTH') or define('INDEX_BLOCK_LENGTH', 12);
defined('TOTAL_HEADER_LENGTH') or define('TOTAL_HEADER_LENGTH', 8192);

/**
 * @see https://github.com/lionsoul2014/ip2region
 */
class IpRegionDB
{
    /**
     * db file handler
     */
    protected $dbFileHandler = null;

    /**
     * header block info
     */
    protected $HeaderSip = null;
    protected $HeaderPtr = null;
    protected $headerLen = 0;

    /**
     * super block index info
     */
    protected $firstIndexPtr = 0;
    protected $lastIndexPtr = 0;
    protected $totalBlocks = 0;

    /**
     * for memory mode only
     * the original db binary string
     */
    protected $dbBinStr = null;
    protected $dbFile = null;

    /**
     * IpRegion constructor.
     *
     * @param string $ip2regionFile
     */
    public function __construct(string $ip2regionFile)
    {
        $this->dbFile = $ip2regionFile;
    }

    /**
     * all the db binary string will be loaded into memory
     * then search the memory only and this will a lot faster than disk base search
     *
     * @Note:
     * invoke it once before put it to public invoke could make it thread safe
     *
     * @param mixed $ip
     *
     * @return array|null
     * @throws
     */
    public function memorySearch($ip)
    {
        //check and load the binary string for the first time
        if ($this->dbBinStr == null) {
            $this->dbBinStr = file_get_contents($this->dbFile);
            if ($this->dbBinStr == false) {
                throw new Exception("Fail to open the db file {$this->dbFile}");
            }
            $this->firstIndexPtr = self::getLong($this->dbBinStr, 0);
            $this->lastIndexPtr = self::getLong($this->dbBinStr, 4);
            $this->totalBlocks = ($this->lastIndexPtr - $this->firstIndexPtr) / INDEX_BLOCK_LENGTH + 1;
        }

        if (is_string($ip)) {
            $ip = self::safeIp2long($ip);
        }

        //binary search to define the data
        $l = 0;
        $h = $this->totalBlocks;
        $dataPtr = 0;
        while ($l <= $h) {
            $m = (($l + $h) >> 1);
            $p = $this->firstIndexPtr + $m * INDEX_BLOCK_LENGTH;
            $sip = self::getLong($this->dbBinStr, $p);
            if ($ip < $sip) {
                $h = $m - 1;
            } else {
                $eip = self::getLong($this->dbBinStr, $p + 4);
                if ($ip > $eip) {
                    $l = $m + 1;
                } else {
                    $dataPtr = self::getLong($this->dbBinStr, $p + 8);
                    break;
                }
            }
        }

        //not matched just stop it here
        if ($dataPtr == 0) {
            return null;
        }

        //get the data
        $dataLen = (($dataPtr >> 24) & 0xFF);
        $dataPtr = ($dataPtr & 0x00FFFFFF);

        return [
            'city_id' => self::getLong($this->dbBinStr, $dataPtr),
            'region'  => substr($this->dbBinStr, $dataPtr + 4, $dataLen - 4),
        ];
    }

    /**
     * get the data block through the specified ip address or long ip numeric with binary search algorithm
     *
     * @param mixed ip
     *
     * @return array|null
     * @throws
     */
    public function binarySearch($ip)
    {
        //check and convert the ip address
        if (is_string($ip)) {
            $ip = self::safeIp2long($ip);
        }

        if ($this->totalBlocks == 0) {
            //check and open the original db file
            if ($this->dbFileHandler == null) {
                $this->dbFileHandler = fopen($this->dbFile, 'r');
                if ($this->dbFileHandler == false) {
                    throw new Exception("Fail to open the db file {$this->dbFile}");
                }
            }
            fseek($this->dbFileHandler, 0);
            $superBlock = fread($this->dbFileHandler, 8);
            $this->firstIndexPtr = self::getLong($superBlock, 0);
            $this->lastIndexPtr = self::getLong($superBlock, 4);
            $this->totalBlocks = ($this->lastIndexPtr - $this->firstIndexPtr) / INDEX_BLOCK_LENGTH + 1;
        }

        //binary search to define the data
        $l = 0;
        $h = $this->totalBlocks;
        $dataPtr = 0;

        while ($l <= $h) {
            $m = (($l + $h) >> 1);
            $p = $m * INDEX_BLOCK_LENGTH;
            fseek($this->dbFileHandler, $this->firstIndexPtr + $p);
            $buffer = fread($this->dbFileHandler, INDEX_BLOCK_LENGTH);
            $sip = self::getLong($buffer, 0);
            if ($ip < $sip) {
                $h = $m - 1;
            } else {
                $eip = self::getLong($buffer, 4);
                if ($ip > $eip) {
                    $l = $m + 1;
                } else {
                    $dataPtr = self::getLong($buffer, 8);
                    break;
                }
            }
        }

        //not matched just stop it here
        if ($dataPtr == 0) {
            return null;
        }

        //get the data
        $dataLen = (($dataPtr >> 24) & 0xFF);
        $dataPtr = ($dataPtr & 0x00FFFFFF);
        fseek($this->dbFileHandler, $dataPtr);
        $data = fread($this->dbFileHandler, $dataLen);

        return [
            'city_id' => self::getLong($data, 0),
            'region'  => substr($data, 4),
        ];
    }

    /**
     * get the data block associated with the specified ip with b-tree search algorithm
     *
     * @Note:
     * not thread safe
     *
     * @param mixed ip
     *
     * @return array|null
     * @throws
     */
    public function btreeSearch($ip)
    {
        if (is_string($ip)) {
            $ip = self::safeIp2long($ip);
        }

        //check and load the header
        if ($this->HeaderSip == null) {
            //check and open the original db file
            if ($this->dbFileHandler == null) {
                $this->dbFileHandler = fopen($this->dbFile, 'r');
                if ($this->dbFileHandler == false) {
                    throw new Exception("Fail to open the db file {$this->dbFile}");
                }
            }
            fseek($this->dbFileHandler, 8);
            $buffer = fread($this->dbFileHandler, TOTAL_HEADER_LENGTH);

            //fill the header
            $idx = 0;
            $this->HeaderSip = [];
            $this->HeaderPtr = [];
            for ($i = 0; $i < TOTAL_HEADER_LENGTH; $i += 8) {
                $startIp = self::getLong($buffer, $i);
                $dataPtr = self::getLong($buffer, $i + 4);
                if ($dataPtr == 0)
                    break;
                $this->HeaderSip[] = $startIp;
                $this->HeaderPtr[] = $dataPtr;
                $idx++;
            }
            $this->headerLen = $idx;
        }

        //1. define the index block with the binary search
        $l = 0;
        $h = $this->headerLen;
        $sptr = 0;
        $eptr = 0;
        while ($l <= $h) {
            $m = (($l + $h) >> 1);

            //perfetc matched, just return it
            if ($ip == $this->HeaderSip[$m]) {
                if ($m > 0) {
                    $sptr = $this->HeaderPtr[$m - 1];
                    $eptr = $this->HeaderPtr[$m];
                } else {
                    $sptr = $this->HeaderPtr[$m];
                    $eptr = $this->HeaderPtr[$m + 1];
                }

                break;
            }

            //less then the middle value
            if ($ip < $this->HeaderSip[$m]) {
                if ($m == 0) {
                    $sptr = $this->HeaderPtr[$m];
                    $eptr = $this->HeaderPtr[$m + 1];
                    break;
                } elseif ($ip > $this->HeaderSip[$m - 1]) {
                    $sptr = $this->HeaderPtr[$m - 1];
                    $eptr = $this->HeaderPtr[$m];
                    break;
                }
                $h = $m - 1;
            } else {
                if ($m == $this->headerLen - 1) {
                    $sptr = $this->HeaderPtr[$m - 1];
                    $eptr = $this->HeaderPtr[$m];
                    break;
                } elseif ($ip <= $this->HeaderSip[$m + 1]) {
                    $sptr = $this->HeaderPtr[$m];
                    $eptr = $this->HeaderPtr[$m + 1];
                    break;
                }
                $l = $m + 1;
            }
        }

        //match nothing just stop it
        if ($sptr == 0)
            return null;

        //2. search the index blocks to define the data
        $blockLen = $eptr - $sptr;
        fseek($this->dbFileHandler, $sptr);
        $index = fread($this->dbFileHandler, $blockLen + INDEX_BLOCK_LENGTH);

        $dataPtr = 0;
        $l = 0;
        $h = $blockLen / INDEX_BLOCK_LENGTH;
        while ($l <= $h) {
            $m = (($l + $h) >> 1);
            $p = (int)($m * INDEX_BLOCK_LENGTH);
            $sip = self::getLong($index, $p);
            if ($ip < $sip) {
                $h = $m - 1;
            } else {
                $eip = self::getLong($index, $p + 4);
                if ($ip > $eip) {
                    $l = $m + 1;
                } else {
                    $dataPtr = self::getLong($index, $p + 8);
                    break;
                }
            }
        }

        //not matched
        if ($dataPtr == 0) {
            return null;
        }

        //3. get the data
        $dataLen = (($dataPtr >> 24) & 0xFF);
        $dataPtr = ($dataPtr & 0x00FFFFFF);

        fseek($this->dbFileHandler, $dataPtr);
        $data = fread($this->dbFileHandler, $dataLen);

        return [
            'city_id' => self::getLong($data, 0),
            'region'  => substr($data, 4),
        ];
    }

    /**
     * safe self::safeIp2long function
     *
     * @param mixed ip
     *
     * @return string
     * @throws
     */
    public static function safeIp2long($ip): string
    {
        $ip = ip2long($ip);

        // convert signed int to unsigned int if on 32 bit operating system
        if ($ip < 0 && PHP_INT_SIZE == 4) {
            $ip = sprintf("%u", $ip);
        }

        return $ip;
    }

    /**
     * read a long from a byte buffer
     *
     * @param string $b
     * @param int    $offset
     *
     * @return string
     */
    public static function getLong(string $b, int $offset): string
    {
        $val = (
            (ord($b[$offset++])) |
            (ord($b[$offset++]) << 8) |
            (ord($b[$offset++]) << 16) |
            (ord($b[$offset]) << 24)
        );

        // convert signed int to unsigned int if on 32 bit operating system
        if ($val < 0 && PHP_INT_SIZE == 4) {
            $val = sprintf("%u", $val);
        }

        return $val;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->dbFileHandler != null) {
            fclose($this->dbFileHandler);
        }

        $this->dbBinStr = null;
        $this->HeaderSip = null;
        $this->HeaderPtr = null;
    }
}
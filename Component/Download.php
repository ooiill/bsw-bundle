<?php

namespace Leon\BswBundle\Component;

use Exception;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;

class Download
{
    /**
     * @var int
     */
    protected $minSpeed = 16;

    /**
     * @var int
     */
    protected $maxSpeed = 1024 * 2;

    /**
     * @var int
     */
    protected $speed = 128;

    /**
     * Download constructor.
     *
     * @param int $speed
     */
    public function __construct(int $speed = null)
    {
        $speed && $this->speed($speed);
    }

    /**
     * Set speed for download
     *
     * @param int $speed
     *
     * @return mixed
     */
    public function speed(int $speed)
    {
        if ($speed > $this->minSpeed || $speed < $this->maxSpeed) {
            return false;
        }

        return $this->speed = $speed;
    }

    /**
     * Get header range info
     *
     * @param int $fileSize
     *
     * @return mixed
     */
    private function getRange(int $fileSize)
    {
        if (!isset($_SERVER['HTTP_RANGE']) || empty($_SERVER['HTTP_RANGE'])) {
            return null;
        }

        $range = $_SERVER['HTTP_RANGE'];
        $range = preg_replace('/[\s|,].*/', null, $range);
        $range = explode('-', substr($range, 6));

        if (count($range) < 2) {
            $range[1] = $fileSize;
        }

        $range = array_combine(['start', 'end'], $range);

        if (empty($range['start'])) {
            $range['start'] = 0;
        }

        if (empty($range['end'])) {
            $range['end'] = $fileSize;
        }

        return $range;
    }

    /**
     * Download location file
     *
     * @param string $filePath
     * @param string $name
     * @param bool   $reload
     *
     * @return void
     * @throws
     */
    public function download(string $filePath, string $name = null, bool $reload = false)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        if (empty($name)) {
            $name = basename($filePath);
        }

        $fp = fopen($filePath, 'rb');
        $fileSize = filesize($filePath);
        $ranges = $this->getRange($fileSize);

        header('Cache-Control: public');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $name);

        if ($reload && $ranges != null) { // use reload
            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges: bytes');
            // surplus length
            header(sprintf('Content-Length: %u', $ranges['end'] - $ranges['start']));
            // range
            header(sprintf('Content-Range: bytes %s-%s/%s', $ranges['start'], $ranges['end'], $fileSize));
            // let the fp goto the prev reload address
            fseek($fp, sprintf('%u', $ranges['start']));
        } else {
            header('HTTP/1.1 200 OK');
            header('Content-Length: ' . $fileSize);
        }

        while (!feof($fp)) {
            sleep(1);
            echo fread($fp, round($this->speed * 1024, 0));
            ob_flush();
        }

        ($fp != null) && fclose($fp);
    }

    /**
     * Download remote file
     *
     * @param string $url
     * @param string $name
     *
     * @return void
     */
    public function remoteDownload(string $url, string $name = null)
    {
        if (empty($name)) {
            $name = basename($url);
        }

        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $name);
        readfile($url);
    }

    /**
     * Remote save file
     *
     * @param string      $url
     * @param string|null $name
     * @param string      $path
     *
     * @return false|string
     */
    public function remoteSave(string $url, string $name = null, string $path = Abs::TMP_PATH)
    {
        if (empty($name)) {
            $name = basename($url);
        }

        $file = Helper::joinString(DIRECTORY_SEPARATOR, $path, $name);
        $result = @file_put_contents($file, fopen($url, 'rb'));

        return $result ? $file : false;
    }

    /**
     * Force download - support download the string
     *
     * @param string $fileName
     * @param string $data
     *
     * @return void
     */
    public function forceDownload(string $fileName = null, string $data = null)
    {
        if (empty($fileName)) {
            return null;
        }

        if ($data === null) {

            if (!(@is_file($fileName) && ($fileSize = @filesize($fileName)) !== false)) {
                return null;
            }

            $filePath = $fileName;
            $fileName = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $fileName));
            $fileName = end($fileName);

        } else {
            $fileSize = strlen($data);
        }

        // Set the default MIME type to send
        $mime = 'application/octet-stream';
        $x = explode('.', $fileName);
        $extension = end($x);

        // http://digiblog.de/2011/04/19/android-and-the-download-file-headers
        if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match(
                '/Android\s(1|2\.[01])/',
                $_SERVER['HTTP_USER_AGENT']
            )
        ) {
            $x[count($x) - 1] = strtoupper($extension);
            $fileName = implode('.', $x);
        }

        if ($data === null && ($fp = @fopen($filePath, 'rb')) === false) {
            return null;
        }

        // Clean output buffer
        if (ob_get_level() !== 0 && @ob_end_clean() === false) {
            @ob_clean();
        }

        // Generate the server headers
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $fileSize);

        // Internet Explorer-specific headers
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }

        header('Pragma: no-cache');

        // If we have raw data - just dump it
        if ($data !== null) {
            exit($data);
        }

        // Flush 1MB chunks of data
        while (!feof($fp) && ($data = fread($fp, round($this->speed * 1024, 0))) !== false) {
            echo $data;
            sleep(1);
        }

        fclose($fp);
        exit(ErrorDebugExit::CODE);
    }
}
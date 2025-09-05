<?php

namespace Rotaz\GeoData\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Exception;
use League\Csv\SyntaxError;
use League\Csv\UnavailableStream;

trait WithDefaultData
{
    protected function storeFromSQL($fileSource)
    {
        $sqlPath = $this->downloadAndExtractZip($fileSource);
        DB::unprepared(file_get_contents($sqlPath));

    }
    public function downloadAndExtractZip($url)
    {
        // Download the ZIP file
        Log::debug('Downloading from URL: ' . $url);
        $fileName = basename($url);
        $zipPath = $this->getTempFolder() . '/' . $fileName;

        Log::debug('Temporary ZIP path: ' . $zipPath);
        $extractTo = $this->getTempFolder();
        file_put_contents($zipPath, fopen($url, 'r'));

        // Extract the ZIP file
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractTo);
            $csvName = $zip->getNameIndex(0); // Assumes only one file in ZIP
            $zip->close();
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            return $extractTo . '/' . $csvName;
        } else {
            throw new \Exception('Failed to open ZIP file at ' . $zipPath);
        }
    }

    public function getTempFolder()
    {
        $temp = sys_get_temp_dir();
        if (!is_dir($temp)) {
            mkdir($temp, 0755, true);
        }

        return $temp;

    }

    /**
     * @throws UnavailableStream
     * @throws SyntaxError
     * @throws Exception
     */
    public function createRecords(string $csvPath): \Iterator
    {
        Log::debug('Creating records from CSV: ' . $csvPath);
        $reader = \League\Csv\Reader::createFromPath($csvPath, 'r');
        $reader->setHeaderOffset(0); // Assuming the first row contains headers
        $header = $reader->getHeader(); // Get the header row
        Log::debug('CSV Header: ' . implode(', ', $header));
        return $reader->getRecords();

    }
}

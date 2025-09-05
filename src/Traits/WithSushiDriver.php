<?php

namespace Rotaz\GeoData\Traits;
use League\Csv\Exception;
use League\Csv\Reader as CSV;
use League\Csv\UnavailableStream;

trait WithSushiDriver
{
    use \Sushi\Sushi;


    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    public function getRows(): array
    {
        if( empty($this->rows) )
            $this->populateRows();

        return $this->rows;
    }

    protected function populateRows()
    {
        $rows = CSV::createFromPath($this->getFilePath())->getRecords();
        $this->rows = iterator_to_array($rows);
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    protected function sushiCacheReferencePath()
    {
        return $this->getFilePath();
    }


}

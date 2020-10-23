<?php
/**
 * @author Alex Kusakin
 */

namespace AlexKusakin\QuoteCsv\Model\Csv;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Quote\Model\Quote;

/**
 * CSV Converter
 * @package AlexKusakin\QuoteCsv\Block\Cart
 */
class Converter
{
    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var DataProvider\QuoteFactory
     */
    protected $dataProviderFactory;

    /**
     * Converter constructor.
     * @param Filesystem $filesystem
     * @param DataProvider\QuoteFactory $dataProviderFactory
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        DataProvider\QuoteFactory $dataProviderFactory
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->dataProviderFactory = $dataProviderFactory;
    }

    /**
     * Get CSV file
     *
     * @param Quote $quote
     * @return array
     * @throws FileSystemException
     */
    public function getCsvFile(Quote $quote)
    {
        $this->directory->create('export');
        $file = $this->getFilename($quote);

        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $dataProvider = $this->dataProviderFactory->create(['quote' => $quote]);
        $stream->writeCsv($dataProvider->getHeaders());

        foreach ($dataProvider->getData() as $row) {
            $stream->writeCsv($row);
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    public function getFilename($quote)
    {
        $hash = md5(microtime());

        return "export/quote_{$quote->getId()}_{$hash}csv";
    }
}

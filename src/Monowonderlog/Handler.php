<?php

namespace hosttech\Monowonderlog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Handler extends AbstractProcessingHandler
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $identifier;

    /**
     * Handler constructor.
     * @param int $level
     * @param bool $bubble
     * @param $url
     * @param $identifier
     */
    public function __construct($level = Logger::DEBUG, $bubble = true, $url, $identifier)
    {
        parent::__construct($level, $bubble);

        $this->url = $url;
        $this->identifier = $identifier;
    }

    public function getFormatter()
    {
        $this->formatter = $this->getDefaultFormatter();
        return $this->formatter;
    }
    
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record) : void
    {
        if (!empty($this->url)) {
            $return = @file_get_contents($this->url, null, stream_context_create(array(
                'http' => array(
                    'method'        => 'POST',
                    'content'       => http_build_query([
                        'json' => $record['formatted']
                    ]),
                    'ignore_errors' => true,
                    'max_redirects' => 0,
                    'header'        => 'Content-type: application/x-www-form-urlencoded',
                ),
            )));

            if ($return === false) {
                throw new \RuntimeException(sprintf('Could not connect to %s', $this->url));
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new Formatter($this->identifier);
    }
}

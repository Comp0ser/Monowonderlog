<?php

namespace hosttech\Monowonderlog;

use Monolog\Handler\AbstractProcessingHandler;

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
        $this->setLevel($level);
        $this->bubble = $bubble;

        $this->url = $url;
        $this->identifier = $identifier;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $return = @file_get_contents($this->url, null, stream_context_create(array(
            'http' => array(
                'method'        => 'POST',
                'content'       => http_build_query([
                    'json' => $record['formatted']
                ]),
                'ignore_errors' => true,
                'max_redirects' => 0,
            ),
        )));

        if ($return === false) {
            throw new \RuntimeException(sprintf('Could not connect to %s', $this->url));
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
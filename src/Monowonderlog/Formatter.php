<?php

namespace hosttech\Monowonderlog;

use Monolog\Formatter\FormatterInterface;

class Formatter implements FormatterInterface
{
    /**
     * @var string
     */
    private $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function format(array $record)
    {
        $additionals = [];

        if (class_exists('\Request')) {

            $additionals[] = [
                'title' => 'url',
                'content' => \Request::url(),
            ];

            $additionals[] = [
                'title' => 'input',
                'content' => \Request::all(),
            ];
        }

        return json_encode([
            'identifier' => $this->identifier,
            'title' => $record['message'],
            'content' => $record['message'],
            'type' => 'error',
            'status' => 'new',
            'additionals' => $additionals,
        ]);
    }

    public function formatBatch(array $records)
    {
        // TODO: Implement formatBatch() method.
    }
}
<?php

namespace CWP\Search\Solr;

use SilverStripe\Dev\Deprecation;
use SilverStripe\FullTextSearch\Solr\Solr;
use SilverStripe\FullTextSearch\Solr\Stores\SolrConfigStore;

/**
 * @deprecated 1.2.0 Use SolrConfigStore_Post in silverstripe/fulltextsearch instead
 */
class CwpSolrConfigStore implements SolrConfigStore
{
    /**
     * @var string
     */
    protected $remote = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        Deprecation::notice(
            '1.2.0',
            'Use SolrConfigStore_Post in silverstripe/fulltextsearch instead',
            Deprecation::SCOPE_CLASS
        );
        $options = Solr::solr_options();

        $this->url = implode('', [
            'http://',
            isset($config['auth']) ? $config['auth'] . '@' : '',
            $options['host'] . ':' . $options['port'],
            $config['path']
        ]);

        if (isset($config['remotepath'])) {
            $this->remote = $config['remotepath'];
        }
    }

    /**
     * @param string $index
     * @param string $file
     * @return void
     */
    public function uploadFile($index, $file)
    {
        $this->uploadString($index, basename($file ?? ''), file_get_contents($file ?? ''));
    }

    /**
     *
     * @param string $index
     * @param string $filename
     * @param string $string
     * @return void
     */
    public function uploadString($index, $filename, $string)
    {
        $targetDir = "{$this->url}/config/$index";

        file_get_contents($targetDir . '/' . $filename, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/octet-stream',
                'content' => (string) $string
            ]
        ]));
    }

    /**
     * @param string $index
     * @return string
     */
    public function instanceDir($index)
    {
        return $this->remote ? "{$this->remote}/$index" : $index;
    }
}
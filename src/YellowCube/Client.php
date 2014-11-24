<?php

namespace YellowCube;

use YellowCube\Art\Article;
use YellowCube\Util\SoapClient;

class Client {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \SoapClient
     */
    protected $service;

    public function __construct(Config $config = null, \SoapClient $service = null) {
        $this->config = $config;
        $this->service = $service;
    }

    /**
     * Mutates specified Article.
     *
     * The Article has a ChangeFlag which indicates if the
     * article should be inserted, updated or deleted.
     *
     * @param Article $article Article to mutate.
     * @return GEN_Response
     */
    public function insertArticleMasterData(Article $article)
    {
        return $this->getService()->InsertArticleMasterData(array(
            'ControlReference' => $this->getControlReferenceByType('ART'),
            'ArticleList' => array(
                'Article' => $article
            )
        ));
    }

    /**
     * Returns a ControlReference for specified type.
     *
     * @param $type
     * @return ControlReference
     */
    protected function getControlReferenceByType($type) {
        $controlReference = new ControlReference();
        return $controlReference
            ->setType($type)
            ->setSender($this->getConfig()->getSender())
            ->setReceiver('YELLOWCUBE')
            ->setTimestamp(date('Ymdhis'))
            ->setOperatingMode('T') // todo: $this->debugMode ? "T" : "P"
            ->setVersion('1.0')
            ->setCommType('SOAP');
    }

    /**
     * @return Config
     */
    protected function getConfig() {
        if (empty($this->config)) {
            $this->config = Config::testConfig();
        }

        return $this->config;
    }

    /**
     * Returns a SoapClient instance to use.
     *
     * @return \SoapClient
     */
    protected function getService() {
        if (empty($this->service)) {
            $this->service = new SoapClient($this->getConfig()->getWsdl(), $this->getConfig()->getSoapClientOptions());
        }

        return $this->service;
    }

}
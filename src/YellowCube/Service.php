<?php

namespace YellowCube;

use YellowCube\ART\Article;
use YellowCube\Util\SoapClient;
use YellowCube\WAB\Order;

/**
 * Provides methods to mutate articles, order articles and list inventory.
 *
 * @package YellowCube
 * @author Adrian Philipp <adrian.philipp@liip.ch>
 */
class Service
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \SoapClient
     */
    protected $client;

    /**
     * Creates a new Service used to connect to YellowCube.
     *
     * @param Config $config Config containing user credentials, if not provided a test config is used.
     * @param \SoapClient $client Custom optional SoapClient.
     */
    public function __construct(Config $config = null, \SoapClient $client = null)
    {
        $this->config = $config;
        $this->client = $client;
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
        return $this->getClient()->InsertArticleMasterData(array(
            'ControlReference' => $this->getControlReferenceByType('ART'),
            'ArticleList' => array(
                'Article' => $article
            )
        ));
    }

    /**
     * Returns the current status of a inserted Article.
     *
     * The article is referenced by its reference number.
     *
     * @param string $reference
     * @return GEN_Response
     */
    public function getInsertArticleMasterDataStatus($reference)
    {
        return $this->getClient()->GetInsertArticleMasterDataStatus(array(
            'ControlReference' => $this->getControlReferenceByType('ART'),
            'Reference' => $reference
        ));
    }

    /**
     * Creates a new customer order.
     *
     * @param Order $order
     *
     * @return GEN_Response
     */
    public function createYCCustomerOrder(Order $order)
    {
        return $this->getClient()->CreateYCCustomerOrder(array(
            'ControlReference' => $this->getControlReferenceByType('WAB'),
            'Order' => $order
        ));
    }

    /**
     * Returns the current status of a customer order specified by its reference.
     *
     * @param string $reference Customer order reference.
     *
     * @return GEN_Response
     */
    public function getYCCustomerOrderStatus($reference)
    {
        return $this->getClient()->GetYCCustomerOrderStatus(array(
            'ControlReference' => $this->getControlReferenceByType('WAB'),
            'Reference' => $reference
        ));
    }

    /**
     * Returns the current order reply of a customer order specified by
     * the customer order number.
     *
     * @param string $reference Customer order reference.
     *
     * @return GEN_Response
     */
    public function GetYCCustomerOrderReply($customerOrderNo = '')
    {
        return $this->getClient()->GetYCCustomerOrderReply(array(
            'ControlReference' => $this->getControlReferenceByType('WAR'),
            'CustomerOrderNo' => $customerOrderNo
        ));
    }

    /**
     * Returns the current status of a customer order specified by its reference.
     *
     * @param string $reference Customer order reference.
     *
     * @return Article[] Article List
     */
    public function getInventory()
    {
        return $this->getClient()->GetInventory(array(
            'ControlReference' => $this->getControlReferenceByType('BAR'),
        ))->ArticleList->Article;
    }

    /**
     * Returns a ControlReference for specified type.
     *
     * @param $type
     * @return ControlReference
     */
    protected function getControlReferenceByType($type)
    {
        $controlReference = new ControlReference();
        return $controlReference
            ->setType($type)
            ->setSender($this->getConfig()->getSender())
            ->setReceiver($this->getConfig()->getReceiver())
            ->setTimestamp(date('Ymdhis'))
            ->setOperatingMode('T')// todo: $this->getConfig()->isDebugMode() ? "T" : "P"
            ->setVersion('1.0')
            ->setCommType('SOAP');
    }

    /**
     * Returns the config to use.
     *
     * @return Config
     */
    protected function getConfig()
    {
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
    protected function getClient()
    {
        if (empty($this->client)) {
            $this->client = new SoapClient($this->getConfig()->getWsdl(), $this->getConfig()->getSoapClientOptions());
        }

        return $this->client;
    }

}
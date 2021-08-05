<?php
declare(strict_types=1);

namespace Ibericode\Vat\Vies;

use SoapClient;
use SoapFault;

class Client
{

    /**
     * @const string
     */
    const URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * @var SoapClient
     */
    private $client;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var object|null
     */
    protected $viesResponse;

    /**
     * Client constructor.
     *
     * @param int $timeout How long should we wait before aborting the request to VIES?
     */
    public function __construct(int $timeout = 10)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $countryCode
     * @param string $vatNumber
     *
     * @return bool
     *
     * @throws ViesException
     */
    public function checkVat(string $countryCode, string $vatNumber) : bool
    {
        try {
            $this->viesResponse = $this->getClient()->checkVat(
                array(
                    'countryCode' => $countryCode,
                    'vatNumber' => $vatNumber
                )
            );
        } catch (SoapFault $e) {
            throw new ViesException($e->getMessage(), $e->getCode());
        }

        return (bool) $this->viesResponse->valid;
    }

    /**
     * @return object|null
     */
    public function getViesResponse()
    {
        return $this->viesResponse;
    }

    /**
     * @return SoapClient
     */
    protected function getClient() : SoapClient
    {
        if ($this->client === null) {
            $this->client = new SoapClient(self::URL, ['connection_timeout' => $this->timeout]);
        }

        return $this->client;
    }
}

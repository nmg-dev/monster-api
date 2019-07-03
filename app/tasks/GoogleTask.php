<?php

use Google\Ads\GoogleAds\Lib\V2\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V2\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V2\LoggerFactory;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\Configuration;

use Google\Ads\GoogleAds\Util\V2\ResourceNames;

class GoogleTask extends \Phalcon\Cli\Task
{
	use ServiceTaskTrait;

	const CUSTOMER_MATCHES = '/customers\/(?P<cid>\d+)/S';

	protected static function service_name() { return 'google'; }

	protected function buildClient($customerId=null) {
		// token builder
		$tokenBuilder = (new OAuth2TokenBuilder())
			->withClientId($this->_config('client_id'))
			->withClientSecret($this->_config('client_secret'))
			->withRefreshToken($this->_access->refresh_token);
		// client builder
		$clientBuilder = (new GoogleAdsClientBuilder())
			->withOAuth2Credential($tokenBuilder->build())
			->withDeveloperToken($this->_config('dev_token'));
		if($customerId)
			$clientBuilder = $clientBuilder->withLoginCustomerId($customerId);
		return $clientBuilder->build();
	}

	protected function _resources_array($response, $fn='getResourceNames') {
		$rets = [];
		foreach($response->$fn() as $val) 
			array_push($rets, $val);
		return $rets;
	}

	protected function retrieve_customers(&$service, $access) {
		
		
		$resp = $service->listAccessibleCustomers();
		// parse response to customer ids
		$resources = $this->_resources_array($resp);

		$pt =  self::CUSTOMER_MATCHES;
		return array_map(function($rname) use($pt) {
			$match = [];
			return preg_match($pt, $rname, $match) ? $match['cid'] : $rname;
		}, $resources);
	}

	protected function retrieve_customer_info(&$service, $customerId) {
		echo "$customerId -- ";
		$resp = $service->getCustomer(ResourceNames::forCustomer($customerId));
		// var_dump($resp);

		
	}

	public function accessAction($limits=10) {
		foreach($this->list_access($limits) as $access) {
			// build grpc client
			$this->_access = $access;
			echo $access->uid.' >> '.PHP_EOL;
			$client = $this->buildClient();

			// build service client
			$service = $client->getCustomerServiceClient();
			// retrieve customer list
			$customers = $this->retrieve_customers($service, $access);

			foreach($customers as $cid) {
				try {
					$cinfo = $this->retrieve_customer_info($service, $cid);
					echo "- OK\n";
				}  catch(\Exception $ex) {
					echo "- ERR\n";
					// continue
					// throw $ex;
					// echo "\n".$ex->getMessage()."\n";
				}
			}
			
			// close the service, finally.
			$service->close();
		}
	}

}
<?php
/**
* Batch modify the domain name's IP, domain name pointing to dynamic network.
* yuenshui@gmail.com
* @于恩水 http://weibo.com
* 
*/
class dnspod {
	private $email;
	private $password;
	private $format = 'json';
	private $lang = 'cn';
	private $error_on_empty = 'yes';
	private $api_domain = 'https://dnsapi.cn/';
	private $recodes = array();

	/**
	* The default debug not open.
	*/
	public $debug = false;

	/**
	* If this property is true, it will be written to the log file debug information, and debug open.
	*/
	public $debug2log = false;

	/**
	* 
	*/
	public $logFile;

	/**
	* localhost IP
	*/
	public $localIP;

	/**
	* If the local hosts have been set, modify the second property is true.
	*/
	public $ignoreLocalIP = false;

	public function __construct($email, $password) {
		$this->email = $email;
		$this->password = $password;
		$this->localIP = $this->getLocalIP();
	}

	public function getLocalIP() {
		return trim(file_get_contents('http://members.3322.org/dyndns/getip'));
	}

	/**
	* $domain string domain. Such as yuenshui.com
	* $recode string second-level domain prefix. Such as www.yuenshui.com is www, office.yuenshui.com is office
	*/
	public function addDomain($domain, $recode) {
		$this->recodes[$domain][$recode] = gethostbyname("{$recode}.{$domain}");
	}

	public function setIP() {
		foreach ($this->recodes as $domain => $item) {
			$domainRcodes = array();
			$domain_id = 0;
			foreach ($item as $recode => $ip) {
				if(!$this->ignoreLocalIP && $ip == $this->localIP) {
					echo "{$recode}.{$domain} IP is {$ip}, no need to update.\r\n";
					continue;
				}
				if($domainRcodes != -1 && $domain_id == 0) {
					$rs = $this->loadRecode($domain);
					$domainRcodes = $rs['records'];
					$domain_id = $rs['domain']['id'];
				}
				$theRecode = array();
				if($domainRcodes == -1) {
					echo "{$recode}.{$domain} recodes get error.\r\n";
					continue;
				}
				foreach ($domainRcodes as $line) {
					if($line['name'] == $recode) {
						$theRecode = $line;
						break;
					}
				}

				if(empty($theRecode)) {
					echo "{$domain} does not exist, create a website in dnspod.cn.\r\n";
				}

				if($theRecode['value'] == $this->localIP) {
					echo "{$recode}.{$domain} IP is {$ip} from dnspod.cn, no need to update.\r\n";
					continue;
				}

				$upData = array(
					'domain_id' => $domain_id,
					'record_id' => $theRecode['id'],
					'sub_domain' => $theRecode['name'],
					'record_type' => $theRecode['type'],
					'record_line' => $theRecode['line'],
					'ttl'	=> $theRecode['ttl'],
					'value' => $this->localIP
				);
				//print($upData);
				$response = $this->api_call('Record.Modify', $upData);
				//var_dump($response);
				echo "{$recode}.{$domain} from {$theRecode['value']} modification of IP is {$upData['value']},";
				if(isset($response['status']['code']) && $response['status']['code'] == 1) {
					echo " success.\r\n";
				}
				else {
					echo " failure.\r\n";
				}
			}
		}
	}

	public function loadRecode($domain) {
		$rep = $this->api_call('Record.List', array('domain' => $domain));
		return isset($rep['domain']['id']) && $rep['domain']['id'] > 0 ? $rep : -1;
	}

	public function api_call($api, $data) {
		if ($api == '' || !is_array($data)) {
			exit('error:parameter error');
		}

		$api = $this->api_domain . $api;
		$data = array_merge(
			$data, 
			array(
				'login_email' => $this->email,
				'login_password' => $this->password,
				'format' => $this->format,
				'lang' => $this->lang,
				'error_on_empty' => $this->error_on_empty
			)
		);

		$result = $this->post_data($api, $data);

		if (!$result) {
			exit('error:call fails');
		}

		$results = @json_decode($result, 1);
		if (!is_array($results)) {
			exit('error:result error');
		}

		if ($results['status']['code'] != 1) {
			exit($results['status']['message']);
		}

		return $results;
	}

	private function post_data($url, $data) {
		if ($url == '' || !is_array($data)) {
			return false;
		}

		$ch = @curl_init();
		if (!$ch) {
			exit('error：the server does not support CURL');
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'DNSPod API dnspod.class/0.1 (yuenshui@126.com)');
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	private function _debug($msg) {
		if($this->debug2log) {
			file_put_contents($this->logFile, $msg);
		}
		elseif($this->debug) {
			echo $msg;
		}
	}
}

<?php

class AutoLetsEncrypt{
	protected $php_dir;
	protected $acme_dir;
	protected $host_dir;
	protected $acme_type;
	protected $cert_dir;

	function __construct($settings = array()){
		$this->php_dir = $settings['php_dir'];
		$this->acme_dir = $settings['acme_dir'];
		$this->host_dir = $settings['host_dir'];
		$this->acme_type = $settings['acme_type'];
		$this->cert_dir = $settings['acme_dir'].'/certs/'.$settings['acme_type'];
	}

	public function issueCertificate($domain, $data=array()){
		$command = sprintf(
			'%1$s -f %2$s/acme-client.phar issue -d %3$s -p %4$s/%5$s',
			$this->php_dir,
			$this->acme_dir,
			implode(':', $data['domains']),
			$this->host_dir,
			$data['directory']
		);

		$output = trim(shell_exec($command));
		$success = strpos($output, 'was successfully created');

		if($success === FALSE){
			echo 'Failure. Command: '.$command.PHP_EOL;
			echo 'Returned the following message: '.$output.PHP_EOL;
			return false;
		}
		echo $output;
		return true;
	}
}
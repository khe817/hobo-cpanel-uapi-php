<?php
/**
 * Class for executing calls to cPanel UAPI
 */
class CPanel_UAPI
{
	private $host = '';
	private $user = '';
	private $pass = '';
	private $headers = array();

	/**
	* Constructor
	* @param string  $ip_address   cPanel IP address
	* @param string  $user         cPanel login username
	* @param string  $pass         cPanel password
	* @param boolean $SSL          if your whm is on SSL ( https ) true, else ( http ) false, http is not recommended
	* @return true
	*/
	public function __construct( $ip_address , $user , $pass, $SSL = true )
	{
		$host = ( $SSL ) ? ( 'https://' . $ip_address . ':2083' ) : ( 'http://' . $ip_address . ':2082' );
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
	}

	/**
	* send_GET_request to cPanel UAPI
	* @param array $params
	* @return mixed
	*/
	private function send_GET_request( $action, $params = array() )
	{
		$curl = curl_init();
		$url = $this->host . '/execute/' . $action . '?' . http_build_query($params);
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $curl, CURLOPT_USERPWD, $this->user . ':' . $this->pass );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_URL, $url );

		$result = json_decode(curl_exec($curl) , true);

		if ( curl_errno($curl) ) {
			throw new Exception('Error Processing Request: ' . curl_error($curl), 1);
		}
		curl_close($curl);

		if ( isset($result['errors']) ) {
			throw new Exception('Error Processing Request: ' . var_export($result['errors'], 1), 1);
		}

		return $result;
	}

	/**
	* send_POST_request to cPanel UAPI
	* @param array $params
	* @return mixed
	*/
	private function send_POST_request( $action, $params = array() )
	{
		$curl = curl_init();
		$url = $this->host . '/execute/' . $action;
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $curl, CURLOPT_USERPWD, $this->user . ':' . $this->pass );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_URL, $url );

		// Set up a POST request with the payload.
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );

		$result = json_decode(curl_exec($curl) , true);

		if ( curl_errno($curl) ) {
			throw new Exception('Error Processing Request: ' . curl_error($curl), 1);
		}
		curl_close($curl);

		if ( isset($result['errors']) ) {
			throw new Exception('Error Processing Request: ' . var_export($result['errors'], 1), 1);
		}

		return $result;
	}

	/**
	 * Use cPanel UAPI functions for the calls
	 *
	 * @param string $module   cPanel UAPI module
	 * @param string $function cPanel UAPI function
	 * @param array  $params
	 * @return mixed
	 */
	public function execute( $module, $function, $params = array(), $request_type = 'GET' )
	{
		$action = $module . '/' . $function;
		switch ( strtolower($request_type) ) {
			case 'post':
				$response = $this->send_POST_request($action, $params);
				break;

			case 'get':
			default:
				$response = $this->send_GET_request($action, $params);
				break;
		}

		return $response;
	}
}
// eof
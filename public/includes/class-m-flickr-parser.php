<?php
class mFlickrFetcher {
	private $userName;
	private $userID;
	private $APIURL;
	private $photoSet;
	private $callURL;

	public $photostream;

	function __construct() {
		$plugin = mFlickrPhotostream::get_instance();

		$this->userName = $plugin->options['username'];
		$this->userID = $plugin->options['userid'];
		$this->APIURL = $plugin->options['apiurl'];
		$this->photoSet = $plugin->options['photoset'];
		$this->callURL = $this->set_callURL();

		$this->photostream = $this->get_photostream();
	}


	function get_photostream() {
		$file = $this->get_file_cURL();
		$data = simplexml_load_string( $file );
		
		return $data;
	}

	
	function set_callURL() {
		if ( $this->photoSet ) {
			$callURL = $this->APIURL . 
						"photoset.gne?" . 
						"nsid=" . $this->userID . "&" . 
						"set=" . $this->photoSet;
		} else {
			$callURL = $this->APIURL . 
						"photos_public.gne?" . 
						"id=" . $this->userID;
		}

		return $callURL;
	}

	
	function get_file_cURL() {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->callURL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		
		$response = curl_exec( $ch );
		curl_close( $ch );
		
		return $response;
	}
}

	
?>

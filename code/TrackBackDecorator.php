<?php
/**
 * Add trackback (receive and send) feature blog entry
 */ 

class TrackBackDecorator extends DataExtension {
	
	static $trackback_server_class = 'TrackbackHTTPServer';
	
	// function extraStatics() {
	// 	return array(
	// 		'has_many' => array(
	// 			'TrackBackURLs' => 'TrackBackURL',
	// 			'TrackBacks' => 'TrackBackPing'
	// 		)
	// 	);
	// }

	static $has_many = array(
		'TrackBackURLs' => 'TrackBackURL',
		'TrackBacks' => 'TrackBackPing'
	);
	
	// function updateCMSFields($fields) {
	// 	// Trackback URL field 
	// 	if($this->owner->TrackBacksEnabled()) {
	// 		$trackbackURLTable = new ComplexTableField(
	// 			$this,
	// 			'TrackBackURLs',
	// 			'TrackBackURL',
	// 			array(
	// 				'URL' => 'URL',
	// 				'IsPung' => 'Pung?'
	// 			), 
	// 			'getCMSFields_forPopup',
	// 			'',
	// 			'ID'
	// 		);	
	// 		$fields->addFieldToTab("Root.Content.Main", $trackbackURLTable);
	// 	}
	// 	else {
	// 		$fields->addFieldToTab("Root.Content.Main", new ReadonlyField("TrackBackURLsReadOnly", _t("BlogEntry.TrackbackURLs", "Trackback URLs"), _t("BlogEntry.TrackbackURLs_DISABLED", "To use this feature, please check 'Enable TrackBacks' check box on the blog holder.")));
	// 	}
	// }

	
	function onBeforePublish() {
		if(!$this->owner->TrackBacksEnabled() && !$this->owner->TrackBackURLs()) return; 
			
		foreach($this->owner->TrackBackURLs() as $trackBackURL) {
			if(!$trackBackURL->Pung && $this->trackbackNotify($trackBackURL->URL)) { 
				$trackBackURL->Pung = true; 
				$trackBackURL->write(); 
			}
		}	
	}
	
	/**
	 * Trackback notify the specified trackback url
	 * @param	boolean | true on success, otherwise false 
	 */
	function trackbackNotify($url) {
		$content = new HTMLText('Content'); 
		$content->setValue($this->owner->Content);
		$excerpt = $content->FirstParagraph();

		if($this->owner->Parent() && $this->owner->ParentID > 0) {
			$blogName = $this->owner->Parent()->Title;
		}
		else {
			$blogName = "";
		}

		$postData = array(
			'url' => $this->owner->AbsoluteLink(),
			'title' => $this->owner->Title,
			'excerpt' => $excerpt, 
			'blog_name' => $blogName
		);
		
		$controller = Object::create(self::$trackback_server_class);
		$response = $controller->request($url, $postData); 

		if($response->getStatusCode() == '200' && stripos($response->getBody(), "<error>0</error>") !== false) {
			return true; 
		}
		
		return false;
	}

	function updateMetaTags(&$tags) {
		$tags .= $this->owner->renderWith('TrackBackRdf');
	}
	
	function TrackBackPingLink() {
		return $this->owner->AbsoluteLink() . 'trackbackping';
	}
	
	function decoratedTrackbackping() {
		$error = 0;
		$message = '';
		
		if(!(isset($_POST['url']) && $_POST['url'])) {
			$error = 1;
			$message = 'Missing required POST parameter \'url\'.';
		} else {
			$trackbackping = new TrackBackPing();
			$trackbackping->Url = $_POST['url'];
			if(isset($_POST['title']) && $_POST['title']) {
				$trackbackping->Title = $_POST['title'];
			}
			if(isset($_POST['excerpt']) && $_POST['excerpt']) {
				$trackbackping->Excerpt = $_POST['excerpt'];
			}
			if(isset($_POST['blog_name']) && $_POST['blog_name']) {
				$trackbackping->BlogName = $_POST['blog_name'];
			}
			$trackbackping->PageID = $this->owner->ID;
			$trackbackping->write();
		}
		
		$returnData = new ArrayData(array(
			'Error' => $error,
			'Message' => $message
		));
		
		return $returnData->renderWith('TrackBackPingReturn');
	}
}

/**
 * Example: 
 * $controller = Object::create('TrackbackHTTPClient');
 * $response = $controller->request(new SS_HTTPRequest('POST', $url, null, $postData));
 */
class TrackbackHTTPServer {

	function __construct() {}
	
	/**
	 * @param string 
	 * @param array
	 * @return SS_HTTPResponse
	 */
	function request($url, $data) {
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		return new SS_HTTPResponse($response, $statusCode);
	}
}

?>

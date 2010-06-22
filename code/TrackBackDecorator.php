<?php
/**
 * TODO: Multiple trackback urls for each blog entry 
 */ 
class TrackBackDecorator extends DataObjectDecorator {
	
	static $trackback_server_class = 'TrackbackHTTPServer';
	
	function extraStatics() {
		return array(
			'db' => array(
				'TrackbackURL' => 'Varchar(2048)',
				'PungTrackbackURL' => 'Varchar(2048)'
			),
			'has_many' => array(
				'TrackBacks' => 'TrackBackPing'
			)
		);
	}
	
	function updateCMSFields($fields) {
		// Trackback URL field 
		if($this->owner->TrackBacksEnabled()) {
			$fields->addFieldToTab("Root.Content.Main", new TextField("TrackbackURL", _t("BlogEntry.TRACKBACKURL", "Trackback URL")), "Content");
		}
		else {
			$fields->addFieldToTab("Root.Content.Main", new ReadonlyField("TrackbackURLReadOnly", _t("BlogEntry.TRACKBACKURL", "Trackback URL"), _t("BlogEntry.TRACKBACKURL_DISABLED", "To use this feature, please check 'Enable TrackBacks' check box on the blog holder.")), "Content");
		}
	}
	
	function onBeforePublish() {
		$owner = $this->owner; 

		if(!empty($owner->TrackbackURL) && $owner->TrackBacksEnabled() && $owner->ShouldTrackbackNotify()) {
		
			if($this->trackbackNotify()) { 
				$owner->PungTrackbackURL = $owner->TrackbackURL;
				$owner->write();
			}
		}
	}
	
	/**
	 * Trackback notify the specified trackback url
	 * @param	boolean | true on success, otherwise false 
	 */
	function trackbackNotify() {
		$owner = $this->owner; 
		
		$content = new HTMLText('Content'); 
		$content->setValue($owner->Content);
		$excerpt = $content->FirstParagraph();
		
		if($owner->Parent() && $owner->ParentID > 0) {
			$blogName = $owner->Parent()->Title;
		}
		else {
			$blogName = "";
		}
		
		$postData = array(
			'url' => $owner->AbsoluteLink(),
			'title' => $owner->Title,
			'excerpt' => $excerpt, 
			'blog_name' => $blogName
		);

		$controller = Object::create(self::$trackback_server_class);
		$response = $controller->request($owner->TrackbackURL, $postData); 

		if($response->getStatusCode() == '200' && stripos($response->getBody(), "<error>0</error>") !== false) {
			return true; 
		}
		
		return false;
		
	}
	
	function ShouldTrackbackNotify() {
		return (trim($this->owner->TrackbackURL) != '' && $this->owner->TrackbackURL != $this->owner->PungTrackbackURL);
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

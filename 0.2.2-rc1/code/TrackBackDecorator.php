<?php

class TrackBackDecorator extends DataObjectDecorator {
	function extraStatics() {
		return array(
			'has_many' => array(
				'TrackBacks' => 'TrackBackPing'
			)
		);
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

?>

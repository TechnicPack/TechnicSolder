<?php

class Donate
{
	private $apiKey;
	private $url;

	public static function factory($url, $apiKey)
	{
		$properties = array("url" => $url, "apiKey" => $apiKey);
		return new Donate($properties);
	}

	public function __construct($properties)
	{
		foreach ($properties as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function getEvents()
	{
		$events = json_decode($this->getUrlContents($this->url . "api/events?api_key=" . $this->apiKey));
		return $events;
	}

	public function getEvent($eventId)
	{
		$event = json_decode($this->getUrlContents($this->url . "api/event/" . $eventId . "?api_key=" . $this->apiKey));

		return $event;
	}

	public function getEventUsers($eventId)
	{	
		$event = json_decode($this->getUrlContents($this->url . "api/event/" . $eventId . "/users?api_key=" . $this->apiKey));

		return $event->users;
	}

	private function getUrlContents($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}
}
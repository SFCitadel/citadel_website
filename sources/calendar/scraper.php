<?php
	// Evalyn Tostado
	// Aug 28th, 2016
	// Scrapes data from eurobay and returns a json response about this year's calendar.
	
	$cache_renew_delay_seconds = 3600; // Default: 3600 (1 hour)
	
	ini_set("max_execution_time", 120); //Because erobay takes forever to respond
	date_default_timezone_set("America/Los_Angeles");
	
	function array_utf8_encode($dat)
	{
		if (is_string($dat))
			return utf8_encode($dat);
		if (!is_array($dat))
			return $dat;
		$ret = array();
		foreach ($dat as $i => $d)
			$ret[$i] = array_utf8_encode($d);
		return $ret;
	}
	
	if(!file_exists("./lasttime.dat"))
		$scrape = true;
	else
	{
		$time = intval(file_get_contents("./lasttime.dat"));
		
		$scrape = (time() - $time > $cache_renew_delay_seconds ? true : false);
	}
	
	if($scrape)
	{
		//Erobay scraper
		
		$time = time();
		$year = date("Y");
		
		$data = array('FromMonthPopup' => 1, 'FromDayPopup' => 1, 
		'FromYearPopup' => $year, 
		'ToMonthPopup' => 12, 
		'ToDayPopup' => 31, 
		'ToYearPopup' => $year + 1, 
		'Separator' => "TAB", 
		'Format' => "usa", 
		'Categories' => "Citadel",
		'Save' => "Download+Events",
		'Op' => "AdminExport",
		'CalendarName' => "Community",
		'FromUserPage' => 1,
		'Amount' => "Month",
		'NavType' => "Absolute",
		'Type' => "Block");
		$postData = http_build_query($data);
		
		$options = array('http' => 
			array('method' => 'POST', 
			'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . "Content-length: " . strlen($postData) . "\r\n", 
			'content' => $postData));
		
		$context = stream_context_create($options);
		$calHandle = fopen("http://www.erobay.com/calendar/Calcium40.pl?CalendarName=Community;Op=AdminExport;FromUserPage=1;Amount=Month;NavType=Absolute;Type=Block", "r", false, $context);
		$calArray = array();
		$calNum = 0;
		
		while(($cal = fgetcsv($calHandle, 0, "	", "\"", "\\")) !== false)
		{
			$calArray[$calNum] = $cal;
			$calNum++;
		}
		
		//print_r($calArray);
		$jsonArray = array();
		
		$count = count($calArray);
		for($i = 0; $i < $count; $i++)
		{
			$date = $calArray[$i][0];
			
			// Find the starting time
			$startTime = $date . " " . $calArray[$i][3] . " " . $calArray[$i][4];
			$startTimeTimestamp = strtotime($startTime);
			
			// Ending time
			$endTime = $date . " " . $calArray[$i][5] . " " . $calArray[$i][6];
			$endTimeTimestamp = strtotime($endTime);
			
			if($startTimeTimestamp > $endTimeTimestamp)
			{
				$endTimeTimestamp = strtotime("+1 day", $endTimeTimestamp);
				$endTime = date("m/d/Y g:i a", $endTimeTimestamp);
			}
			
			$title = $calArray[$i][1];
			$description = $calArray[$i][2];
			$location = $calArray[$i][13];
			$subjects = explode("^", $calArray[$i][12]);
			
			$jsonArray[$i] = array('title' => $title, 
			'description' => $description, 
			'startTimeString' => $startTime, 
			'startTimeUnix' => $startTimeTimestamp, 
			'endTimeString' => $endTime, 
			'endTimeUnix' => $endTimeTimestamp,
			'subjects' => $subjects,
			'location' => $location);
		}
		
		$json = array_utf8_encode($jsonArray);
		$json = json_encode($json, JSON_PRETTY_PRINT);
		
		if($json === false)
		{
			exit(json_last_error_msg());
		}
		
		print($json);
		
		file_put_contents("./cache.json", $json);
		file_put_contents("./lasttime.dat", time());
	}
	else
	{
		print(file_get_contents("./cache.json"));
	}
	
	
?>
<?php
	
	class BookingSystem{

		private $con;
		public $userID;
		public $userBranch;
		public $past_termStart;
		public $past_termEnd;
		public $cur_termStart;
		public $cur_termEnd;
		public $future_termStart;
		public $future_termEnd;
		public $today;

		public function __construct($userBranch)
		{
			$host = 'localhost';
			$user = 'show981111';
			$pass = 'dyd1@tmdwlsfl';
			$db = 'show981111';
			$this->con = mysqli_connect($host, $user, $pass, $db) or die('Unable to connect');
			$this->userBranch = $userBranch;

		}

		function getTermList($isEchoNeeded)
		{
			$query = "SELECT startDate,endDate FROM TERMLIST order by UNIX_TIMESTAMP(startDate) ASC "; // get termstart and termEnd 
			$result = mysqli_query($this->con,$query);

			date_default_timezone_set("Asia/Seoul");
			$this->today = date('Y-m-d', time());
			//echo $today;
			$termList = array();
			$count = 0;
			$todayIndex = 0;
			$tempS;
			$tempE;
			$termS;
			$termE;
			if($result)
			{
				while($row = mysqli_fetch_array($result)){
					
					$tempS = $termS;
					$tempE = $termE;
				
					$termS = strtotime(date("Y-m-d",strtotime($row[0]) ));
					$termE = strtotime(date("Y-m-d",strtotime($row[1]) ));

					if(strtotime($this->today) >= $termS && strtotime($this->today) <= $termE )
					{
						$this->cur_termStart = $row[0];
						$this->cur_termEnd = $row[1];
						$this->past_termStart = date("Y-m-d", $tempS );
						$this->past_termEnd = date("Y-m-d", $tempE );
						array_push($termList, array("termStart"=>$this->past_termStart, "termEnd"=>$this->past_termEnd ));
						array_push($termList, array("termStart"=>$this->cur_termStart, "termEnd"=>$this->cur_termEnd ));
						$todayIndex = $count;
					}
					if( ($todayIndex + 1) == $count)
					{
						$this->future_termStart = $row[0];
						$this->future_termEnd = $row[1];
						array_push($termList, array("termStart"=>$this->future_termStart, "termEnd"=>$this->future_termEnd ));
					}
					$count = $count + 1;
				}
				//echo $this->termStart." ".$this->termEnd;
			}
			if($isEchoNeeded == "yes")
			{
				echo json_encode($termList,JSON_UNESCAPED_UNICODE);
			}
			

		}

		function __destruct()
		{
			mysqli_close($this->con);
		}

		function getUser($userID,$userBranch)
		{
			
			$query = "SELECT userID,userBranch,userDuration,userName FROM USER WHERE userID = '$userID' AND userBranch = '$userBranch' ";
			$result = mysqli_query($this->con,$query);

			$response = array();

			if($result)
			{
				while($row = mysqli_fetch_array($result)){
		
					array_push($response, array("userID"=>$row[0], "userBranch"=>$row[1], "userDuration"=>$row[2],"userName"=>$row[3] ));
				}
			}

			echo json_encode($response,JSON_UNESCAPED_UNICODE);
		}

		function getCourseTimeLine()
		{
			$query = "SELECT courseTeacher,courseDay,startTime,endTime FROM COURSETIMELINE WHERE courseBranch = '$this->userBranch' ";
			$result = mysqli_query($this->con,$query);

			$response = array();

			if($result)
			{
				while($row = mysqli_fetch_array($result)){
		
					array_push($response, array("courseTeacher"=>$row[0], "courseDay"=>$row[1], "startTime"=>$row[2], "endTime" => $row[3]));
				}
			}

			echo json_encode($response,JSON_UNESCAPED_UNICODE);
		}

		function getTimeForMonth($courseDay, $courseTeacher, $startDate, $userDuration,$userName)//BOOKEDLIST 를 기준으로 필터링한다 따라서 다음학기예정인 사람같은 경우는 필터안한다
		{
			//echo "step1";
			$timeList = array();
			//if startDate is before termstart -> put fail 
			$this->getTermList("no");
			if(strtotime(date("Y-m-d", strtotime($startDate) ) ) < strtotime(date("Y-m-d", strtotime($this->cur_termStart) )) )
			{
				array_push($timeList, array("regular_Time" => "FAIL" ) );
				echo $timeList;
				return 0;
			}
			$FormattedTermEnd;
			if($userName != "admin")
			{
				$FormattedTermEnd = date("Y-m-d", strtotime($this->cur_termEnd));
				//if user is student -> if startDate is after future Term start -> put fail
				if(strtotime(date("Y-m-d", strtotime($startDate) ) ) > strtotime(date("Y-m-d", strtotime($this->future_termStart) )) )
				{
					array_push($timeList, array("regular_Time" => "FAIL" ) );
					echo $timeList;
					return 0;
				}
			}else
			{
				$FormattedTermEnd = date("Y-m-d", strtotime($this->future_termEnd));
			}
			


			$query = "SELECT startTime, endTime FROM COURSETIMELINE WHERE courseBranch = '$this->userBranch' AND courseDay = '$courseDay' AND courseTeacher = '$courseTeacher' ";

			$result = mysqli_query($this->con, $query);

			$count = 0;
			if($result)
			{

				while($row = mysqli_fetch_array($result)){

					$FormattedStart = date("H:i", strtotime($row[0]));
					$FormattedEnd = date("H:i", strtotime($row[1]));
					//echo " //FormattedStart ".$FormattedStart. " FormattedEnd ".$FormattedEnd. "// ";
					$strStart = strtotime($FormattedStart);
					$strEnd = strtotime($FormattedEnd);

					$candidateStartTime = $strStart;
					if($userDuration == "45")
					{
						$candidateEndTime = strtotime('+45 minutes',$candidateStartTime);
					}else if($userDuration == "30")
					{
						$candidateEndTime = strtotime('+30 minutes',$candidateStartTime);
					}else if($userDuration == "60")
					{
						$candidateEndTime = strtotime('+60 minutes',$candidateStartTime);
					}else{
						return;
					}

					$dayofweek = date('w', strtotime($startDate));

					$FormattedStartDate =  date("Y-m-d", strtotime($startDate));
					$strStartDate = strtotime($FormattedStartDate);

					// $FormattedTermEnd = date("Y-m-d", strtotime($this->cur_termEnd));

					//echo " //Term End".$FormattedTermEnd. "//";
					
					while($candidateStartTime < $strEnd && $candidateEndTime <= $strEnd)
					{	
						//echo " //candidateStartTime ".date("H:i",$candidateStartTime). " candidateEndTime ".date("H:i",$candidateEndTime). "// ";
						
						$flag = 1; // 해당 시간대가 4주를 다 통과햇나 체크하는 거 
						//시간대 하나 뽑았다 그 해당 하나의 시간대에 대해서 4주짜리가 다있는지 체크해줘야한다. 
						//echo "-----------!";
						while($strStartDate <= strtotime($FormattedTermEnd))
						{
							//$combinedDT = date('Y-m-d H:i:s', strtotime("$date $time"));
							$Formats = date('Y-m-d', $strStartDate);
							$Formatst = date('H:i', $candidateStartTime);
							$Formatet = date('H:i', $candidateEndTime);

							$cand_StartDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatst")); 
							$cand_EndDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatet")); 
							//echo "candidate DATETIME".$cand_StartDateTime ."//";

							$selectBookedDate =  "SELECT startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$this->userBranch' AND courseTeacher = '$courseTeacher' AND status = 'BOOKED' ";
							$resDate = mysqli_query($this->con, $selectBookedDate);
							if($resDate)
							{
								while($rowDate = mysqli_fetch_array($resDate))
								{
									//echo " //rowDate ".$rowDate[0]. " //rowed". $rowDate[1];

									$formatRow = date('Y-m-d H:i',strtotime($rowDate[0]));
									$formatRow1 =  date('Y-m-d H:i',strtotime($rowDate[1]));
									//(strtotime($cand_StartDateTime) >= strtotime($formatRow) && strtotime($cand_StartDateTime)<= strtotime($formatRow1)) || (strtotime($cand_EndDateTime) >= strtotime($formatRow) && strtotime($cand_EndDateTime)<= strtotime($formatRow1))
									if( (strtotime($cand_StartDateTime) >= strtotime($formatRow) && strtotime($cand_StartDateTime) < strtotime($formatRow1)) || (strtotime($cand_EndDateTime) > strtotime($formatRow) && strtotime($cand_EndDateTime)<= strtotime($formatRow1)) )
									{
										$flag = 0;
										break;
									}else
									{
										$flag = 1;
									}
								}
							}

							if($flag == 0) break;

							$strStartDate = strtotime("+7 day",$strStartDate);
							//echo " FFFFFLAG ".$flag;
						}
						$strStartDate = strtotime($FormattedStartDate);

						//echo "!-------------";

						if($flag == 1)
						{
							//echo "pushed ". date("H:i",$candidateStartTime);
							array_push($timeList, array("regular_Time" => date("H:i",$candidateStartTime) ) );
						}	
						

						if($userDuration == "45")
						{
							$candidateStartTime = strtotime('+45 minutes',$candidateStartTime);
							$candidateEndTime = strtotime('+45 minutes',$candidateEndTime);
						}else if($userDuration == "30")
						{
							$candidateStartTime = strtotime('+30 minutes',$candidateStartTime);
							$candidateEndTime = strtotime('+30 minutes',$candidateEndTime);
						}else if($userDuration == "60")
						{
							$candidateStartTime = strtotime('+60 minutes',$candidateStartTime);
							$candidateEndTime = strtotime('+60 minutes',$candidateEndTime);
						}
						
						//$candidateStartTime = strtotime($FormattedcandidateStartTime);
						//$candidateEndTime = strtotime($FormattedcandidateEndTime)
					}



				}
			}
			echo json_encode($timeList,JSON_UNESCAPED_UNICODE);



		}
		function getAddedTime($userDuration, $addedTime )//String , time object 
		{
			$resTime;
			if($userDuration == "45")
			{
				$resTime = strtotime('+45 minutes',$addedTime);
			}else if($userDuration == "30")
			{
				$resTime = strtotime('+30 minutes',$addedTime);
			}else if($userDuration == "60")
			{
				$resTime = strtotime('+60 minutes',$addedTime);
			}
			return $resTime;
		}

		function getTimeForDay($userID,$userDuration, $selectedDate, $userName, $courseTeacher, $courseBranch, $dow)
		{
			$r_courseBranch = $courseBranch;
			$r_courseTeacher = $courseTeacher;
			$response = array();
			if($userName != "admin")
			{
				//get courseTeacher and branch from RegularSchedule
				$r_courseTeacher;
				$r_courseBranch;
				$getDataFromR = "SELECT courseBranch,courseTeacher FROM REGULARSCHEDULE WHERE userID = '$userID' ";
				$res_getDataFromR = mysqli_query($this->con,$getDataFromR);
				if(mysqli_num_rows($res_getDataFromR ) > 0)
				{
					while($resData = mysqli_fetch_array($res_getDataFromR))
					{
						$r_courseBranch = $resData[0];
						$r_courseTeacher = $resData[1];
					}
				}else{
					echo json_encode($response,JSON_UNESCAPED_UNICODE);
				}
			}
			$this->getTermList("no");
			if(strtotime($selectedDate) < strtotime($this->today) )
			{
				echo $response;
				return;
			}
			$openList = array();
			$getOpen = "SELECT startDate, endDate FROM OPENDATE WHERE courseBranch = '$r_courseBranch' AND courseTeacher = '$r_courseTeacher' ";
			$getOpenRes = mysqli_query($this->con,$getOpen);
			while($openRow = mysqli_fetch_array($getOpenRes))
			{
				$startDateFormat = date("Y-m-d", strtotime($openRow[0]));
				if(strtotime($startDateFormat) == strtotime($selectedDate))
				{
					$tempStartOpen =  date("H:i", strtotime($openRow[0]));
					$tempEndOpen = date("H:i", strtotime($openRow[1]));
					array_push($openList, $tempStartOpen, $tempEndOpen );
				}
			}

			$getTimeQuery = "SELECT startTime, endTime FROM COURSETIMELINE WHERE courseBranch = '$r_courseBranch' AND courseDay = '$dow' AND courseTeacher = '$r_courseTeacher' ";
			$res_getTimeQuery = mysqli_query($this->con, $getTimeQuery);
			if(mysqli_num_rows($res_getTimeQuery ) > 0)
			{
				$count = 0;
				
				while( ($row = mysqli_fetch_array($res_getTimeQuery)) || ( ($count + 2) <= count($openList)) )
				{
					$tempStartTime;
					$tempEndTime;
					if($row)//mysqli_fetch_array($res_getTimeQuery) != null
					{
						$tempStartTime = strtotime($row[0]);
						$tempEndTime = strtotime($row[1]);
					}else{
						$tempStartTime = strtotime($openList[$count]);
						$tempEndTime = strtotime($openList[$count+1]);
						$count = $count + 2;
					}
					

					$addedStartTime = $tempStartTime;
					$addedEndTime = $this->getAddedTime($userDuration, $addedStartTime);
					$formatDate = date('Y-m-d', strtotime($selectedDate));
					while($addedStartTime < $tempEndTime)
					{
						$formatTimeS = date('H:i', $addedStartTime);
						$formatTimeE = date('H:i', $addedEndTime);

						$candidateTimeS =  date('Y-m-d H:i',strtotime("$formatDate $formatTimeS")); 
						$candidateTimeE = date('Y-m-d H:i',strtotime("$formatDate $formatTimeE")); 
						//echo "CANDIDATE S E ".$candidateTimeS. " ~ ". $candidateTimeE . "////";

						$filter = "SELECT startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$r_courseBranch' AND courseTeacher = '$r_courseTeacher'AND status = 'BOOKED' order by UNIX_TIMESTAMP(startDate) DESC ";
						$filterRes = mysqli_query($this->con,$filter);
						$flag = 1;
						while($filterRow = mysqli_fetch_array($filterRes) )//check if that candidate is available
						{
							$filterRow0Format = date('Y-m-d H:i', strtotime($filterRow[0]));
							$filterRow1Format = date('Y-m-d H:i', strtotime($filterRow[1]));
							//echo "FILTER S E ".$filterRow0Format. " ~ ". $filterRow1Format . "////";
							if(strtotime($filterRow0Format) < strtotime($this->today)){ break; }
							if( (strtotime($candidateTimeS) >= strtotime($filterRow0Format) && strtotime($candidateTimeS) < strtotime($filterRow1Format)) || (strtotime($candidateTimeE) > strtotime($filterRow0Format) && strtotime($candidateTimeE)<= strtotime($filterRow1Format)) )
							{
								$flag = 0;
								break;
								//not available
							}
						}

						$closeFilter = "SELECT start,endDate,courseTeacher, courseBranch FROM EXCLUSION WHERE (courseTeacher = '$r_courseTeacher' OR courseTeacher = '전체') AND (courseBranch = '$r_courseBranch' OR courseBranch = '전체') order by UNIX_TIMESTAMP(start) DESC ";
						$closeFilterRes = mysqli_query($this->con,$closeFilter);
						while($closeFilterRow = mysqli_fetch_array($closeFilterRes))
						{
							if(strtotime($closeFilterRow[0]) < strtotime($this->today)){ break; }
							$closeFilterRowS = date('Y-m-d H:i', strtotime($closeFilterRow[0]));
							$closeFilterRowE = date('Y-m-d H:i', strtotime($closeFilterRow[1]));
							//echo "FILTER S E ".$filterRow0Format. " ~ ". $filterRow1Format . "////";
							
							if( (strtotime($candidateTimeS) >= strtotime($closeFilterRowS) && strtotime($candidateTimeS) < strtotime($closeFilterRowE)) || (strtotime($candidateTimeE) > strtotime($closeFilterRowS) && strtotime($candidateTimeE)<= strtotime($closeFilterRowE)) )
							{
								$flag = 0;
								break;
								//not available
							}
						}

						if($flag == 1)
						{
							array_push($response, array("regular_Time" => date("H:i",$addedStartTime) ) );
						}

						$addedStartTime = $this->getAddedTime($userDuration, $addedStartTime);
						$addedEndTime = $this->getAddedTime($userDuration, $addedEndTime);
					}
				}

				echo json_encode($response,JSON_UNESCAPED_UNICODE);
			}else{
				echo json_encode($response,JSON_UNESCAPED_UNICODE);//no time for this timeline 
			}
		}

		function cancelCourse($userID,$cancelTeacher, $cancelBranch, $startDate, $endDate)
		{
			
			$checkCredit = "SELECT userCredit FROM USER WHERE userID = '$userID' ";
			$checkCreditRes = mysqli_query($this->con,$checkCredit);
			if($checkCreditRes)
			{
				while($row = mysqli_fetch_array($checkCreditRes)){
					if($row[0] <= 0)
					{
						echo "creditOver";
						return;
					}
				
				}
			}
			date_default_timezone_set("Asia/Seoul");
			$todayDate = date('Y-m-d H:i', time());
			$limitTime = strtotime('-4 hours', strtotime($startDate));
			
			if(strtotime($todayDate) >= $limitTime  )
			{
				echo "timeout";
				return;
			}

			$checkRedunt = "SELECT * FROM BOOKEDLIST WHERE userID = '$userID' AND courseTeacher = '$cancelTeacher' AND courseBranch = '$cancelBranch' AND startDate = '$startDate' AND endDate = '$endDate' AND status <> 'BOOKED' AND status <> 'changeDone' ";
			$checkRes = mysqli_query($this->con,$checkRedunt);
			if(mysqli_num_rows($checkRes) > 0)
			{
				echo "already";//이미 취소한 수업인지 체크하는 곳... 취소했다가 그날 새로예약을 잡았다면 그수업은 체인지 돈... -> 또 그날 취소 할수도 있으니까.. 체인지 돈도 제외 
				return;
			}


			$cancelQuery = "UPDATE BOOKEDLIST SET status = 'canceled' WHERE userID = '$userID' AND courseTeacher = '$cancelTeacher' AND courseBranch = '$cancelBranch' AND startDate = '$startDate' AND endDate = '$endDate' ";
			$setCredit = "UPDATE USER SET userCredit = userCredit - 1 WHERE userID = '$userID' ";

			$cancelres = mysqli_query($this->con, $cancelQuery);

			if(mysqli_affected_rows($this->con) > 0)
			{
				$setres = mysqli_query($this->con, $setCredit);
				if(mysqli_affected_rows($this->con) > 0)
				{
					$response = "success";
				}else{
					$response = "fail";
				}

			}else
			{
				$response = "fail";
			}
			echo $response;
		}

		function getBookedList($userID, $option)
		{
			$fetchStart = "";
			$fetchEnd = "";

			$this->getTermList("no");
			if($option == "cur")
			{
				$fetchStart= $this->cur_termStart;
				$fetchEnd = $this->cur_termEnd;
			}else if($option == "cancelAll" || $option == "changeRes" || $option == "cancelCheck")
			{
				$fetchStart= $this->past_termStart;
				$fetchEnd = $this->cur_termEnd;
			}else if($option == "cancel_cur")
			{
				$fetchStart= $this->cur_termStart;
				$fetchEnd = $this->cur_termEnd;
			}
			else
			{
				$fetchStart= $this->past_termStart;
				$fetchEnd = $this->past_termEnd;
			}
			$response = array();
			if($fetchStart != "" && $fetchEnd != "")
			{
				$query = "";
				if($option == "cancel_cur")
				{
					$query = "SELECT courseTeacher,courseBranch,startDate,endDate,status FROM BOOKEDLIST WHERE userID = '$userID' AND ( status = 'canceled' OR status = 'changeDone') order by UNIX_TIMESTAMP(startDate) DESC ";//??????
				}else if($option == "cancelAll" || $option == "cancelCheck")
				{
					$query = "SELECT courseTeacher,courseBranch,startDate,endDate,status FROM BOOKEDLIST WHERE userID = '$userID' AND (status = 'canceled' OR status = 'closeCanceled') order by UNIX_TIMESTAMP(startDate) DESC ";//이번학기 지난학기 취소한 수업을 알기위한 쿼리
				}else if($option == "changeRes")
				{
					$query = "SELECT A.courseTeacher,A.courseBranch,A.startDate,A.endDate,A.changeFrom,A.status FROM BOOKEDLIST A LEFT JOIN BOOKEDLIST B ON A.changeFrom = B.startDate WHERE A.userID = '$userID' AND ( A.status <> 'changeDone' AND A.status <> 'extending')  order by UNIX_TIMESTAMP(A.startDate) DESC ";//
				}
				else
				{
					$query = "SELECT courseTeacher,courseBranch,startDate,endDate,status FROM BOOKEDLIST WHERE userID = '$userID' AND status = 'BOOKED' order by UNIX_TIMESTAMP(startDate) DESC ";//예약되어있는 수업을 파싱
				}
				if($query != "")
				{
					$result = mysqli_query($this->con,$query);

					if($result)
					{
						while($row = mysqli_fetch_array($result)){
							$startMonth = date("Y-m-d",strtotime($row[2]));
							$endMonth = date("Y-m-d",strtotime($row[3]));
							if(strtotime($startMonth) < strtotime($fetchStart) && strtotime($endMonth) < strtotime($fetchStart)  )
							{
								break;//만약 시작날 끝나는 둘다 파싱하려는 시작날보다 작다면 바로 브레이크 내림차순 정렬이니까 
							}
							if( strtotime($fetchStart) <= strtotime($startMonth) && strtotime($endMonth) <= strtotime($fetchEnd) )
							{	
								if($option == "changeRes")
								{
									array_push($response, array("bookedTeacher"=>$row[0], "bookedBranch"=>$row[1], "bookedStartDate"=>$row[2], "bookedEndDate" => $row[4],"status"=>$row[5] ));
								}else{
									array_push($response, array("bookedTeacher"=>$row[0], "bookedBranch"=>$row[1], "bookedStartDate"=>$row[2], "bookedEndDate" => $row[3], "status" => $row[4]));
								}
							}
							
						}

					}
				}else
				{
					if($option != "cancelCheck")
					{
						echo "fail";
					}
					return $response;
				}
				
			}
			if($option != "cancelCheck")
			{
				echo json_encode($response,JSON_UNESCAPED_UNICODE);
			}
			return $response;
		}

		function putExtendTerm($start, $end)//학기 연장은 수정이 거의 불가능하니까 존나 신중해야됨 
		{
			$response;
			if($start == "" && $end == "")
			{
				$this->getTermList("no");
				$start = $this->future_termStart;
				$end = $this->future_termEnd;
			}else{

				if(strtotime($this->today) >= strtotime($start) )//오늘보다 이전의 날짜로 연장할수 없음 
				{
					$response = "fail";
					echo $response;
					return 0;
				}

				$start =  date("Y-m-d", strtotime($start));
				$end =  date("Y-m-d", strtotime($end));
				$this->getTermList("no");

				if(strtotime($start) >= strtotime($this->future_termStart) && strtotime($start) < strtotime($this->future_termEnd) )
				{
					$response = "redunt";
					echo $response;
					return 0;
				}//already extended in termList
				if(strtotime($end) >= strtotime($this->future_termStart) && strtotime($end) < strtotime($this->future_termEnd) )
				{
					$response = "redunt";
					echo $response;
					return 0;
				}//already extended in termList 

				$query = "UPDATE TERMLIST SET startDate = '$start', endDate = '$end' order by UNIX_TIMESTAMP(startDate) ASC LIMIT 1 ";
				$result = mysqli_query($this->con,$query);
				if(mysqli_affected_rows($this->con) > 0)
				{
					$response = "success";
				}else
				{
					$response = "fail";
					return $response;
				}
			}
			//echo $start." ";


			$getRegular = "SELECT courseTeacher,courseBranch,userID,startTime,endTime,dow,startDate FROM REGULARSCHEDULE WHERE extendedDate <> '$start' AND status <> 'false' ";//가장 최근 정기예약 날짜 확인 
			$result1 = mysqli_query($this->con,$getRegular);                                                    //이미 연장된 학생은 뺼수잇도록 
			$addedStart= strtotime($start);
			//$dayofweek = date('w', strtotime($startDate));
			$regularStart;

			if(mysqli_num_rows ( $result1 ) <= 0){
				$response = "nothing";
			}

			while($row = mysqli_fetch_array($result1))
			{
				$formatStartTime = date('H:i',strtotime($row[3]));
				$formatEndTime = date('H:i',strtotime($row[4]));
				//echo "//format Start ".$formatStartTime. " ". "formatEndTime ".$formatEndTime." dow".$row[5]." " .date('w', $addedStart) ." //";
				//$cand_StartDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatst")); 
				
				if(strtotime($row[6]) > strtotime($start) )//if startDate is after termStart 
				{
					$addedStart = strtotime($row[6]);
				}else{
					while(date('w', $addedStart) != $row[5])//find the first Date that matches dow 다음학기 정기 첫날짜 찾기 위해서 
					{
						$addedStart = strtotime("+1 day",$addedStart);
						//echo " //addedStart ". date("Y-m-d",$addedStart);
					}
				}

				$regularStart = date('Y-m-d',$addedStart);
				$cand_StartDateTime = date('Y-m-d H:i',strtotime("$regularStart $formatStartTime")); 
				$cand_EndDateTime = date('Y-m-d H:i',strtotime("$regularStart $formatEndTime")); 
				//echo " //cand start ".$cand_StartDateTime. " cand End ".$cand_EndDateTime;

				$cand_endDate = date('Y-m-d',strtotime($cand_EndDateTime));

				while(strtotime($cand_endDate) <= strtotime($end) ) // +7씩 해나가면서 텀 끝날때까지 삽입 해준다! 
				{
					$insertquery = "INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, ownerID, status) VALUES ('$row[0]', '$row[1]', '$cand_StartDateTime', '$cand_EndDateTime', '$row[2]', '$row[2]', 'BOOKED'  ) ";
					$pushquery = mysqli_query($this->con,$insertquery);
					//$strStartDate = strtotime("+7 day",$strStartDate);
					$cand_StartDateTime = date('Y-m-d H:i',strtotime("+7 day", strtotime($cand_StartDateTime))); 
					$cand_EndDateTime = date('Y-m-d H:i',strtotime("+7 day", strtotime($cand_EndDateTime))); 
					$cand_endDate = date('Y-m-d',strtotime($cand_EndDateTime));
					//echo "////INSIDE while cand start ".$cand_StartDateTime. " cand End ".$cand_EndDateTime." END WHILE////";
				}


				$insertRecentExtend = "UPDATE REGULARSCHEDULE SET extendedDate = '$start' WHERE userID = '$row[2]' ";
				$updatequery = mysqli_query($this->con,$insertRecentExtend);       
				if(mysqli_affected_rows($this->con) > 0)
				{
					$response = "success";
				}else
				{
					$response = "notupdated";
				}
			}



			echo $response;
			return $response;
		}
		function getDowKorean($num)
		{
			$dow;
			switch ($num) {
				case "0":
					$dow = "일";
					break;
				case "1":
					$dow = "월";
					break;
				case "2":
					$dow = "화";
					break;
				case "3":
					$dow = "수";
					break;
				case "4":
					$dow = "목";
					break;
				case "5":
					$dow = "금";
					break;
				case "6":
					$dow = "토";
					break;
			}
			return $dow;
		}

		function getWaitList()
		{
			$query = "SELECT courseTeacher,courseBranch,userID,startTime,endTime,dow,startDate FROM WAITLIST order by UNIX_TIMESTAMP(askDate) ASC";
			$result = mysqli_query($this->con, $query);
			$response = array();

			if($result)
			{
				while($row = mysqli_fetch_array($result)){
					$dow = $this->getDowKorean($row[5]);
					$startDateAndDow = $row[6]. " ".$dow;
					$timeTillEnd = $row[3]. " ~ ".$row[4];
					array_push($response, array("wl_userID"=>$row[2], "wl_userBranch"=>$row[1], "wl_courseTeacher"=>$row[0], "wl_startDate" => $startDateAndDow, "wl_Time" => $timeTillEnd));
				}
			}

			echo json_encode($response,JSON_UNESCAPED_UNICODE); 
		}

		function putWaitList($courseTeacher, $courseBranch, $userID, $startTime, $endTime, $dow,$startDate, $todayDateTime)
		{
			$response = "fail";

			if(strtotime($todayDateTime) > strtotime($startDate))
			{
				echo "past";
				return;
			}

			$findNow = "SELECT startTime, endTime, dow FROM REGULARSCHEDULE WHERE userID = 'userID' AND courseTeacher = '$courseTeacher' AND courseBranch = '$courseBranch' ";

			$NowRes = mysqli_query($this->con,$findNow);
			if(mysqli_num_rows ( $NowRes ) > 0)
			{
				$response = "alreadyBooked";
				echo $response;
				return 0;

			}

			$findquery = "SELECT * FROM WAITLIST WHERE courseTeacher = '$courseTeacher' AND courseBranch = '$courseBranch' AND userID = '$userID' AND startTime = '$startTime' AND endTime = '$endTime' AND dow = '$dow' AND startDate = '$startDate' ";

			$result = mysqli_query($this->con,$findquery);

			if(mysqli_num_rows ( $result ) > 0)
			{
				$response = "already";
				echo $response;
				return 0;

			}

			$insertquery = "INSERT INTO WAITLIST (courseTeacher, courseBranch, userID, startTime, endTime, dow, startDate, askDate) VALUES ('$courseTeacher', '$courseBranch', '$userID', '$startTime', '$endTime', '$dow', '$startDate', '$todayDateTime'  ) ";

			$pushquery = mysqli_query($this->con,$insertquery);

			if($pushquery)
			{
				$response = "success";
			}else
			{
				$response = "internet_fail";
			}

			echo $response;

		}

		function acceptRegular($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$dow,$userName)
		{
			$response;
			$this->getTermList("no");
			$selectSame = "SELECT * FROM REGULARSCHEDULE WHERE courseTeacher = '$pt_courseTeacher'AND courseBranch = '$pt_courseBranch'AND startTime = '$startTime'AND endTime = '$endTime'AND dow = '$dow' AND  userID = '$pt_userID' ";
			$sameRes = mysqli_query($this->con,$selectSame);
			if(mysqli_num_rows ( $sameRes ) > 0)
			{
				$response = "already";
				if($userName != "admin")
				{
					$response = $this->deleteWaitList($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$dow);
					if($response == "success")
					{
						$response = "already";
					}
				}
				echo $response;
				return 0;
				
			}
			//filterMonth($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $endChangeDate)
			$filterRes = $this->filterMonth($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $this->cur_termEnd, $dow);
			if($filterRes == "notEmpty")//해당 정기예약이 비어있지 않는 경우...
			{
				echo "notEmpty";
				return 0;
			}else
			{
				$updatequery = "UPDATE REGULARSCHEDULE SET courseTeacher = '$pt_courseTeacher',courseBranch = '$pt_courseBranch', startTime = '$startTime', endTime = '$endTime', startDate = '$startDate',dow = '$dow',extendedDate = '$this->cur_termStart' WHERE userID = '$pt_userID' ";
				$updateResult = mysqli_query($this->con,$updatequery);
				if(mysqli_affected_rows($this->con) > 0)
				{
					$response = "success";
				}else
				{
					$insertquery = "INSERT INTO REGULARSCHEDULE (courseTeacher, courseBranch, startTime, endTime, dow, startDate, extendedDate, userID) VALUES ('$pt_courseTeacher', '$pt_courseBranch', '$startTime', '$endTime', '$dow', '$startDate', '$this->cur_termStart', '$pt_userID'  ) ";

					$insertResult = mysqli_query($this->con,$insertquery);
					if(mysqli_affected_rows($this->con) > 0)
					{
						$response = "success";
					}else
					{
						$response = "internet_fail_insert_Regular";
						echo $response;
						return 0;
					}
				}

				if(strtotime($startDate) >= strtotime($this->cur_termStart) && strtotime($startDate) <= strtotime($this->cur_termEnd) )//현 학기를 신청햇다면 
				{	

					$response = $this->changeRegular($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID, $this->cur_termEnd);//유저가 신청했을 경우 현학기 내부에서만 조절 가능하므로 끝나는 날은 현학기 종료일
					//echo $response. "changeRegular";
					echo $userName;
					if($response == "success" && $userName != "admin")
					{
						$response = $this->deleteWaitList($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$dow,"no");
					}
				}
				echo $response;
			}
		}

		function changeRegular($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$endChangeDate)
		{
			$response;
			$selectquery = "SELECT startDate FROM BOOKEDLIST WHERE userID = '$pt_userID' AND status = 'BOOKED' AND ownerID = '$pt_userID' ";
			$fetch = mysqli_query($this->con,$selectquery);
			if(mysqli_num_rows ( $fetch ) > 0)// 기존 사용자의 경우 기존 정기예약이 있겟지!
			{
				while($row = mysqli_fetch_array($fetch))
				{
					$FormatStartDate = date('Y-m-d', strtotime($row[0]));

					$changedStartDate = strtotime($startDate);
					$tochangeStartDate = strtotime($FormatStartDate);

					$isSameWeek = date('oW', $changedStartDate) === date('oW', $tochangeStartDate) && date('Y', $changedStartDate) === date('Y', $tochangeStartDate);
					if($isSameWeek || strtotime($startDate) < strtotime($row[0]))
					{
						$deletequery = "DELETE FROM BOOKEDLIST WHERE userID = '$pt_userID' AND status = 'BOOKED' AND ownerID = '$pt_userID' AND startDate = '$row[0]' ";
						$delete = mysqli_query($this->con,$deletequery);
						if(mysqli_affected_rows($this->con) > 0)
						{
							$response = "success";
						}else
						{
							$response = "internet_fail_delete_BookedList";
							return $response;
						}
					}//같은 주에 있는 거부터 변경된 날 이후로 다 삭제
					

				}
				
				// 아래랑 똑같다 ;; //
				$FormatStart = date('Y-m-d', strtotime($startDate) );
				$FormatStartTime = date('H:i', strtotime($startTime));
				$FormatEndTime = date('H:i', strtotime($endTime));

				$tempStart = date('Y-m-d H:i',strtotime("$FormatStart $FormatStartTime")); 
				$tempEnd = date('Y-m-d H:i',strtotime("$FormatStart $FormatEndTime")); 

				while(strtotime($tempStart) <= strtotime($endChangeDate) && strtotime($tempStart) <= strtotime($this->cur_termEnd) )//시작부터 끝까지(최대 현학기 끝) 삽입해줘 
				{
					$insertNewDate = "INSERT BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, ownerID, status) VALUES ('$pt_courseTeacher', '$pt_courseBranch', '$tempStart', '$tempEnd', '$pt_userID', '$pt_userID', 'BOOKED'  ) ";
					$insertNewRes = mysqli_query($this->con, $insertNewDate);
					if($insertNewRes)
					{
						$response = "success";
					}else
					{
						$response = "internet_fail_insert_BookedList";
					}

					
					$tempStart = date('Y-m-d H:i',strtotime("+7 day",strtotime($tempStart) ));
					$tempEnd = date('Y-m-d H:i',strtotime("+7 day",strtotime($tempEnd) ));
					//echo " FFFFFLAG ".$flag;
				}
				// 아래랑 똑같다 ;; //
				
			}else
			{
				// 아래랑 똑같다 ;; //
				$FormatStart = date('Y-m-d', strtotime($startDate) );
				$FormatStartTime = date('H:i', strtotime($startTime));
				$FormatEndTime = date('H:i', strtotime($endTime));

				$tempStart = date('Y-m-d H:i',strtotime("$FormatStart $FormatStartTime")); 
				$tempEnd = date('Y-m-d H:i',strtotime("$FormatStart $FormatEndTime")); 

				//echo $tempStart. " ". $tempEnd. " before";
				while(strtotime($tempStart) <= strtotime($endChangeDate) && strtotime($tempStart) <= strtotime($this->cur_termEnd) )//시작부터 끝까지(최대 현학기 끝) 삽입해줘 
				{
					$insertNewDate = "INSERT BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, ownerID, status) VALUES ('$pt_courseTeacher', '$pt_courseBranch', '$tempStart', '$tempEnd', '$pt_userID', '$pt_userID', 'BOOKED'  ) ";
					$insertNewRes = mysqli_query($this->con, $insertNewDate);
					if($insertNewRes)
					{
						$response = "success";
						//echo $tempStart. " ". $tempEnd. " llll";
					}else
					{
						$response = "internet_fail_insert_BookedList";
					}
					
					$tempStart = date('Y-m-d H:i',strtotime("+7 day",strtotime($tempStart) ));
					$tempEnd = date('Y-m-d H:i',strtotime("+7 day",strtotime($tempEnd) ));
					//echo " FFFFFLAG ".$flag;
				}
				// 아래랑 똑같다 ;; //
			}

			return $response;
		}

		function deleteWaitList($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $pt_userID,$dow,$isEchoNeeded)
		{
			//echo $pt_courseTeacher." ".$pt_courseBranch." ".$startTime. " ".$endTime. " ".$pt_userID." ".$dow." ".$startDate;
			$deleteWait = "DELETE FROM WAITLIST WHERE courseTeacher = '$pt_courseTeacher' AND courseBranch ='$pt_courseBranch' AND startTime = '$startTime' AND endTime = '$endTime' AND startDate = '$startDate' AND dow = '$dow' AND userID = '$pt_userID' ";

			$deleteRes = mysqli_query($this->con,$deleteWait);

			if(mysqli_affected_rows($this->con) > 0)
			{
				$response = "success";
			}else
			{
				$response = "internet_fail_delete_WaitList";
			}
			if($isEchoNeeded == "yes")
			{
				echo "delete_success";
			}
			return $response;
		}

		function filterMonth($pt_courseTeacher, $pt_courseBranch,$startTime, $endTime ,$startDate, $endChangeDate, $dow)
		{
			$Formats = date('Y-m-d', strtotime($startDate));
			$Formatst = date('H:i', strtotime($startTime));
			$Formatet = date('H:i', strtotime($endTime));
			$flag = 1;
			$cand_StartDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatst")); 
			$cand_EndDateTime = date('Y-m-d H:i',strtotime("$Formats $Formatet")); 
			//echo "candidate DATETIME".$cand_StartDateTime ."//";
			if(strtotime($cand_StartDateTime) > strtotime($endChangeDate))//다음학기의 예약인 경우 
			{
				$selectSame = "SELECT * FROM REGULARSCHEDULE WHERE courseTeacher = '$pt_courseTeacher'AND courseBranch = '$pt_courseBranch'AND startTime = '$startTime'AND endTime = '$endTime'AND dow = '$dow' AND  status <> 'false' ";
				$sameRes = mysqli_query($this->con,$selectSame);
				if(mysqli_num_rows ( $sameRes ) > 0)
				{
					return "notEmpty";
				}
			}
			while(strtotime($cand_StartDateTime) <= strtotime($endChangeDate))
			{
				//날짜 하나에 대해서 검사
				$selectBookedDate =  "SELECT startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$pt_courseBranch' AND courseTeacher = '$pt_courseTeacher' AND status = 'BOOKED' ";
				$resDate = mysqli_query($this->con, $selectBookedDate);
				if($resDate)
				{
					while($rowDate = mysqli_fetch_array($resDate))
					{
						//echo " //rowDate ".$rowDate[0]. " //rowed". $rowDate[1];

						$formatRow = date('Y-m-d H:i',strtotime($rowDate[0]));
						$formatRow1 =  date('Y-m-d H:i',strtotime($rowDate[1]));
						
						if( (strtotime($cand_StartDateTime) >= strtotime($formatRow) && strtotime($cand_StartDateTime) < strtotime($formatRow1)) || (strtotime($cand_EndDateTime) > strtotime($formatRow) && strtotime($cand_EndDateTime)<= strtotime($formatRow1)) )
						{
							$flag = 0;
							break;
						}else
						{
							$flag = 1;
						}
					}
				}

				if($flag == 0) break;

				$cand_StartDateTime = date('Y-m-d H:i',strtotime("+7 day",strtotime($cand_StartDateTime) ));
				$cand_EndDateTime = date('Y-m-d H:i',strtotime("+7 day",strtotime($cand_EndDateTime) ));
				//$endTime = date("H:i",strtotime('+60 minutes',$tmpTime));
			}

			if($flag == 0)
			{
				return "notEmpty";
			}else
			{
				return "success";
			}
		}

		function putNewlyDate($courseTeacher,$courseBranch, $userID, $startDate, $canceledDate, $userDuration, $userName)
		{
			$this->getTermList("no");

			if( strtotime($this->today) > strtotime($startDate) )
			{
				echo "past";
				return;
			}
			
			
			if( $userName != "admin")
			{
				// check if there are canceled Course 
				if(count($this->getBookedList($userID, "cancelCheck")) <= 0)
				{
					echo "noCanceled";//if no canceld Course, cannot book newly Date
					return;
				}
				date_default_timezone_set("Asia/Seoul");
				$todayDate = date('Y-m-d H:i', time());
				$limitTime = strtotime('-4 hours', strtotime($startDate));
				if($userID != "admin")
				{
					if(strtotime($todayDate) >= $limitTime  )
					{
						echo "timeout";
						return;
					}
				}
				if(strtotime($this->future_termStart) <= strtotime($startDate))
				{
					echo "future";
					return;
				}
				
				$getDataFromR = "SELECT courseBranch,courseTeacher FROM REGULARSCHEDULE WHERE userID = '$userID' ";
				$res_getDataFromR = mysqli_query($this->con,$getDataFromR);
				if(mysqli_num_rows($res_getDataFromR ) > 0)
				{
					while($resData = mysqli_fetch_array($res_getDataFromR))
					{
						$r_courseBranch = $resData[0];
						$r_courseTeacher = $resData[1];
					}
				}else{
					echo "noRegular";
					return;
				}
			}
			if($canceledDate != "admin")
			{
				$already = "SELECT * FROM BOOKEDLIST WHERE userID = '$userID' AND changeFrom = '$canceledDate' ";
				$alreadyRes = mysqli_query($this->con,$already);
				if(mysqli_num_rows($alreadyRes) > 0)
				{
					echo "already";
					return;
				}
			}
			
			$candidateTimeS = date('Y-m-d H:i', strtotime($startDate));
			$temp = $this->getAddedTime($userDuration, strtotime($candidateTimeS));
			$candidateTimeE = date('Y-m-d H:i', $temp);

			$filter = "SELECT startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$r_courseBranch' AND courseTeacher = '$r_courseTeacher'AND status = 'BOOKED' ";
			$filterRes = mysqli_query($this->con,$filter);
			$isBooked = 0;
			while($filterRow = mysqli_fetch_array($filterRes) )//check if that candidate is available
			{
				$filterRow0Format = date('Y-m-d H:i', strtotime($filterRow[0]));
				$filterRow1Format = date('Y-m-d H:i', strtotime($filterRow[1]));
				//echo "FILTER S E ".$filterRow0Format. " ~ ". $filterRow1Format . "////";

				if( (strtotime($candidateTimeS) >= strtotime($filterRow0Format) && strtotime($candidateTimeS) < strtotime($filterRow1Format)) || (strtotime($candidateTimeE) > strtotime($filterRow0Format) && strtotime($candidateTimeE)<= strtotime($filterRow1Format)) )
				{
					$isBooked = 1;
					break;
					//not available
				}
			}
			if($isBooked == 1)
			{
				echo "isBooked";
				return;
			}
			//"INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, ownerID, status) VALUES ('$row[0]', '$row[1]', '$cand_StartDateTime', '$cand_EndDateTime', '$row[2]', '$row[2]', 'BOOKED'  ) ";
			$put = "INSERT INTO BOOKEDLIST (courseTeacher, courseBranch, startDate, endDate, userID, status, changeFrom) VALUES ('$r_courseTeacher', '$r_courseBranch', '$startDate', '$candidateTimeE', '$userID','BOOKED','$canceledDate'  ) ";
			$putquery = mysqli_query($this->con,$put);

			if(mysqli_affected_rows($this->con) > 0)
			{
				if($canceledDate != "admin")//널로 넣어주는 것은 체인지 돈으로 업글해줄 필요가 읍다 체인지 돈이면 그 수업으로 보강 잡을 수 없다. 
				{
					$updateStatus = "UPDATE BOOKEDLIST SET status = 'changeDone' WHERE userID = '$userID' AND courseBranch = '$r_courseBranch' AND startDate = '$canceledDate' AND status = 'canceled'  ";
					$updatequery = mysqli_query($this->con, $updateStatus);
					if(mysqli_affected_rows($this->con) > 0)
					{
						echo "success";
						return;
					}else{
						echo "fail";
						return;
					}
				}
				echo "success";
				return;
			}else{
				echo "fail";
				return;
			}
		}

		function extendRequest($userID, $extendTeacher,$extendBranch,$extendStartDate, $extendEndDate )
		{	
			$this->getTermList("no");

			date_default_timezone_set("Asia/Seoul");
			$todayDate = date('Y-m-d H:i', time());
			$limitTime = strtotime('-4 hours', strtotime($extendStartDate));
			if($userID != "admin")
			{
				if(strtotime($todayDate) >= $limitTime  )
				{
					echo "timeout";
					return;
				}
			}
			
			$response;	
			$dow = date('w',strtotime($extendEndDate));
			$dowKorean = $this->getDowKorean($dow);
			$checkCourseTimeLine = "SELECT startTime, endTime FROM COURSETIMELINE WHERE courseTeacher = '$extendTeacher' AND courseBranch = '$extendBranch' AND courseDay = '$dowKorean'  ";
			$checkRes = mysqli_query($this->con,$checkCourseTimeLine);
			$flag = 1;
			while($checkResRow = mysqli_fetch_array($checkRes))
			{
				$tempTimeS = date('H:i', strtotime($extendStartDate));
				$tempTimeE = date('H:i', strtotime($extendEndDate));//booked Start and EndDate
				if(strtotime($tempTimeE) < strtotime($checkResRow[1]) && strtotime($tempTimeS) >= strtotime($checkResRow[0])  )
				{
					$flag = 1;
					break;
				}else{
					$flag = 0;
				}
			}
			if($flag == 0){
				echo "notEmpty";
				return;
			}
			$selectNotEmpty = "SELECT * FROM BOOKEDLIST WHERE courseTeacher = '$extendTeacher' AND courseBranch = '$extendBranch' AND startDate ='$extendEndDate'  ";
			$selectNotEmptyRes = mysqli_query($this->con,$selectNotEmpty);
			if(mysqli_num_rows ( $selectNotEmptyRes ) > 0)
			{
				echo "notEmpty";
				return;
			}
			
			$selectExtending = "SELECT courseTeacher, courseBranch, startDate, endDate, status,extendedMin FROM BOOKEDLIST WHERE userID = '$userID' AND status = 'extending' order by UNIX_TIMESTAMP(startDate) ASC ";
			$selectRes = mysqli_query($this->con,$selectExtending);
			if(mysqli_num_rows ( $selectRes ) > 0)
			{
				while($row = mysqli_fetch_array($selectRes))
				{
					if(strtotime($row[2]) >= strtotime($this->past_termStart) && strtotime($row[3]) <= strtotime($this->cur_termEnd) )
					{
						$startDateTime = new DateTime($row[2]);
						$endDateTime = new DateTime($row[3]); 
						$interval = $startDateTime->diff($endDateTime);
						$availableExtendMinute = $interval->format('%i');//30
				
						$tempint = (int)$row[5];
						$newExtendedMin = 15+ $tempint ;
						$tempAEM = (int)$availableExtendMinute ;
						
						if( $newExtendedMin <=$tempAEM ){
							$tempExtend = strtotime($extendEndDate);
							
							$extendedEndDate = date("Y-m-d H:i",strtotime('+15 minutes',$tempExtend) );
							
							$extendBooked = "UPDATE BOOKEDLIST SET endDate = '$extendedEndDate', changeFrom = '$row[2]' WHERE userID = '$userID' AND courseTeacher = '$extendTeacher' AND courseBranch = '$extendBranch' AND startDate = '$extendStartDate'  ";
							$updateExtend = mysqli_query($this->con,$extendBooked);
							if(mysqli_affected_rows($this->con) > 0)
							{
								
								$extendExtending = "UPDATE BOOKEDLIST SET extendedMin = '$newExtendedMin' WHERE userID = '$userID' AND courseTeacher = '$row[0]' AND courseBranch = '$row[1]' AND startDate = '$row[2]' AND endDate = '$row[3]' AND status = 'extending'  ";
								$extendExtendingRes = mysqli_query($this->con, $extendExtending);
								if(mysqli_affected_rows($this->con) > 0)
								{
									$response = "success";
									echo $extendedEndDate;
									return $extendedEndDate;
								}else{
									$response = "fail";
									echo $response;
									return;
								}

							}else{
								$response = "fail";
								echo $response;
								return;
							}
						}else{
							$response = "unavailable";
						}
					}else{
						$response = "unavailable";
					}

					
				}
			}
			//extending 상태인것을 써먹지 못햇을 경우 
			
			$selectCanceled = "SELECT courseTeacher, courseBranch, startDate, endDate, status FROM BOOKEDLIST WHERE userID = '$userID' AND status <> 'BOOKED' AND status <> 'extending' order by UNIX_TIMESTAMP(startDate) ASC ";
			$selectCanceledRes = mysqli_query($this->con,$selectCanceled);
			if(mysqli_num_rows($selectCanceledRes) > 0)
			{
				while($rowCancel = mysqli_fetch_array($selectCanceledRes))
				{
					if(strtotime($rowCancel[2]) >= strtotime($this->past_termStart) && strtotime($rowCancel[3]) <= strtotime($this->cur_termEnd) ){
						$tempExtend = strtotime($extendEndDate);
						$extendedEndDate = date("Y-m-d H:i",strtotime('+15 minutes',$tempExtend) );
						$extendBooked = "UPDATE BOOKEDLIST SET endDate = '$extendedEndDate', changeFrom = '$rowCancel[2]' WHERE userID = '$userID' AND courseTeacher = '$extendTeacher' AND courseBranch = '$extendBranch' AND startDate = '$extendStartDate'  ";
						$updateExtend = mysqli_query($this->con,$extendBooked);
						if(mysqli_affected_rows($this->con) > 0)
						{
							$updateExtending = "UPDATE BOOKEDLIST SET extendedMin = '15', status = 'extending' WHERE userID = '$userID' AND courseTeacher = '$rowCancel[0]' AND courseBranch = '$rowCancel[1]' AND startDate = '$rowCancel[2]' AND endDate = '$rowCancel[3]' AND status = '$rowCancel[4]'  ";
							$updateExtendingRes = mysqli_query($this->con, $updateExtending);
							if(mysqli_affected_rows($this->con) > 0)
							{
								$response = "success";
								echo $extendedEndDate;
								return $extendedEndDate;
							}else{
								$response = "fail";
								echo $response;
								return;
							}
						}else{
							$response = "fail";
							echo $response;
							return;
						}
					}
				}



			}else{
				echo "unavailable";
				return;
			}
				
			
		}

		function getAllBookedList($userID,$userBranch,$courseTeacher, $fetchStart, $fetchEnd,$option )
		{
			$response = array();
			if($option == "init" || $option == "every")
			{
				$mainTeacher = "none";
				$checkRegular = "SELECT courseTeacher FROM REGULARSCHEDULE WHERE userID = '$userID' ";
				$res = mysqli_query($this->con, $checkRegular);
				while($resrow = mysqli_fetch_array($res))
				{
					$mainTeacher = $resrow[0];

				}
				array_push($response, array("mainTeacher" => $mainTeacher));
				if($mainTeacher == "none" || $option == "every")
				{
					$query = "SELECT userID, courseTeacher, startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$userBranch' AND status = 'BOOKED' order by UNIX_TIMESTAMP(startDate) DESC ";
				}else{
					$query = "SELECT userID, courseTeacher, startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$userBranch' AND status = 'BOOKED' AND courseTeacher = '$mainTeacher' order by UNIX_TIMESTAMP(startDate) DESC ";
				}
				
			}else if($option == "own")
			{
				$query = "SELECT userID, courseTeacher, startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$userBranch' AND status = 'BOOKED' AND userID = '$userID' order by UNIX_TIMESTAMP(startDate) DESC ";
				

			}else if($option == "teacher")
			{
				$query = "SELECT userID, courseTeacher, startDate, endDate FROM BOOKEDLIST WHERE courseBranch = '$userBranch' AND status = 'BOOKED' AND courseTeacher = '$courseTeacher' order by UNIX_TIMESTAMP(startDate) DESC ";
				
			}
			$openQuery = "SELECT startDate, endDate, courseTeacher, courseBranch, num FROM OPENDATE WHERE courseBranch = '$userBranch' AND  order by UNIX_TIMESTAMP(startDate) DESC ";
			$closeQuery = "SELECT start, endDate, courseTeacher, courseBranch, rendering FROM EXCLUSION WHERE courseBranch = '$userBranch' OR courseBranch = '전체' order by UNIX_TIMESTAMP(start) DESC ";

			$fetchStartDate = date('Y-m-d', strtotime($fetchStart));
			$fetchEndDate = date('Y-m-d',strtotime($fetchEnd));

			$fetchQuery = mysqli_query($this->con,$query);
			

			while($row = mysqli_fetch_array($fetchQuery))
			{	

				if(strtotime($row[2]) >= strtotime($fetchStartDate) &&  strtotime($row[3]) <= strtotime($fetchEndDate))
				{
					array_push($response, array("id"=>$row[2]." ".$row[0], "title"=>$row[0], "resourceId" => $row[1], "start" => $row[2], "end" => $row[3] ));
				}else if(strtotime($row[2]) < strtotime($fetchStartDate))
				{
					break;
				}
				
        		//array_push($termList, array("termStart"=>$this->cur_termStart, "termEnd"=>$this->cur_termEnd ));
     		}

			
			$openQueryRes = mysqli_query($this->con, $openQuery);
			$closeQueryRes = mysqli_query($this->con, $closeQuery); 

			while($row = mysqli_fetch_array($openQueryRes)){
				if(strtotime($row[0]) < strtotime($fetchStartDate)  &&  strtotime($row[1]) <= strtotime($fetchEndDate))
				{
					array_push($response, array("id" => "open".$row[4] , "title" => "대체수업/예약해주세요", "start"=>$row[0], "end"=>$row[1], "resourceId" => $row[2], "overlap" => "true"));
				}else if(strtotime($row[0]) < strtotime($fetchStartDate)){
					break;
				}
			}

			while($row = mysqli_fetch_array($closeQueryRes)){
				if($row[2] == '전체')
				{
					if(strtotime($row[0]) >= strtotime($fetchStartDate) &&  strtotime($row[1]) <= strtotime($fetchEndDate) )
					{
						array_push($response, array("start"=>$row[0], "end"=>$row[1], "courseBranch" => $row[3], "rendering" => $row[4], "color" => "DimGray"));
					}else{
						break;
					}
				}else
				{
					if(strtotime($row[2]) >= strtotime($fetchStartDate) &&  strtotime($row[1]) <= strtotime($fetchEndDate) )
					{
						array_push($response, array("start"=>$row[0], "end"=>$row[1], "resourceId"=>$row[2],"courseBranch" => $row[3], "rendering" => $row[4], "color" => "DimGray"));
					}else if(strtotime($row[0]) < strtotime($fetchStartDate)){
						break;
					}
				}
			}
			

			echo json_encode($response,JSON_UNESCAPED_UNICODE);

		}






	}

	//$userBranch = $_POST['userBranch'];
	//$test = new BookingSystem($userBranch);
	//$test->getCourseTimeLine();
	//$test->getUser($userBranch);

?>
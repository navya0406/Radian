<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\DB;
use Requests\Request as R;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use App\Repositories\Radian\RadianRepository;
class RadianController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
        public function __construct(RadianRepository $RadianRepository){
            
                    $this->RadianRepository = $RadianRepository;
        }
        public function issuesBySearch() {
            $inputUserData = \Input::all();
            $projectId = !empty($inputUserData['project_id'])?$inputUserData['project_id']:'';
            $search = !empty($inputUserData['search'])?$inputUserData['search']:'';
            $limit = !empty($inputUserData['limit'])?$inputUserData['limit']:25;
            $offset = !empty($inputUserData['offset'])?$inputUserData['offset']:0;
            $totalIssues = $this->RadianRepository->getAllIssues($projectId);
            $resultSet = $this->RadianRepository->getIssues($projectId,$search,$limit,$offset);
            if(!empty($resultSet)){
                foreach($resultSet as $res){
                   $issueDetails = array();
                   $issueDetails['id'] = $res->issue_id;
                   $issueDetails['project']['id']  = $res->project_id;
                   $issueDetails['project']['name']  = $res->project_name;
                   $issueDetails['tracker']['id']  = $res->tracker_id;
                   $issueDetails['tracker']['name']  = $res->tracker_name;
                   $issueDetails['status']['id']  = $res->status_id;
                   $issueDetails['status']['name']  = $res->status_name;
                   $issueDetails['priority']['id']  = $res->priority_id;
                   $issueDetails['priority']['name']  = $res->priority_name;
                   $issueDetails['author']['id']  = $res->author_id;
                   $issueDetails['author']['name']  = $res->author_name;
                   $issueDetails['assigned_to']['id']  = $res->assigned_to_id;
                   $issueDetails['assigned_to']['name']  = $res->assigned_to_name;
                   $issueDetails['fixed_version']['id']  = $res->fixed_version_id;
                   $issueDetails['fixed_version']['name']  = $res->fixed_version_name;
                   $issueDetails['subject'] = $res->subject;
                   $issueDetails['description'] = $res->description;
                   $issueDetails['due_date'] = $res->due_date;
                   $issueDetails['done_ratio'] = $res->done_ratio;
                   $issueDetails['custom_fields'][0]['id']  = $res->cs_id_1;
                   $issueDetails['custom_fields'][0]['name']  = $res->cs_name_1;
                   $issueDetails['custom_fields'][0]['value']  = $res->cs_value_1;
                   $issueDetails['custom_fields'][1]['id']  = $res->cs_id_2;
                   $issueDetails['custom_fields'][1]['name']  = $res->cs_name_2;
                   $issueDetails['custom_fields'][1]['value']  = $res->cs_value_2;
                   $issueDetails['custom_fields'][2]['id']  = $res->cs_id_3;
                   $issueDetails['custom_fields'][2]['name']  = $res->cs_name_3;
                   $issueDetails['custom_fields'][2]['value']  = $res->cs_value_3;
                   $issueDetails['custom_fields'][3]['id']  = $res->cs_id_4;
                   $issueDetails['custom_fields'][3]['name']  = $res->cs_name_4;
                   $issueDetails['custom_fields'][3]['value']  = $res->cs_value_4;
                   $issueDetails['custom_fields'][4]['id']  = $res->cs_id_5;
                   $issueDetails['custom_fields'][4]['name']  = $res->cs_name_5;
                   $issueDetails['custom_fields'][4]['value']  = $res->cs_value_5;
                   $issueDetails['custom_fields'][5]['id']  = $res->cs_id_6;
                   $issueDetails['custom_fields'][5]['name']  = $res->cs_name_6;
                   $issueDetails['custom_fields'][5]['value']  = $res->cs_value_6;
                   $issueDetails['created_on'] = $res->created_on;
                   $issueDetails['updated_on'] = $res->updated_on;
                   $returnData[] = $issueDetails;

                }
                 $data = array("issues" => array_values($returnData), "total_count" => count($totalIssues), "limit" => $limit,"offset" => $offset);
                $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly retrieved') + $data;
                
            }else{
                $data = array();
                 $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'No records found')+ $data;
            }
            $jsonData = \Response::json($finalResult);
            return $jsonData;
        } 
        public function addTime(){
              $body = array();
              $inputUserData = \Input::all();
              $body['time_entry']['project_id'] = $inputUserData['project_id'];
              $body['time_entry']['issue_id'] = !empty($inputUserData['issue_id'])?$inputUserData['issue_id']:'';
              $body['time_entry']['hours'] = $inputUserData['hours'];
              $body['time_entry']['activity_id'] = $inputUserData['activity_id'];
              $body['time_entry']['comments'] = $inputUserData['comments'];
              $body['time_entry']['spent_on'] = $inputUserData['spent_on'];
              $userName = $inputUserData['username'];
              $password = $inputUserData['password'];
              $client = new GuzzleHttpClient();
              $url = 'http://radian.enterpi.com/redmine/time_entries.json';
              $response = $client->request("POST", $url, ['auth' => [$userName,$password],'form_params'=>$body]);
              $radianUrl = 'http://'.$userName.':'.$password.'@radian.enterpi.com/redmine/time_entries.json?user_id='.$inputUserData['user_id'].'&limit=1';
              $radianDetails = $this->urlFileContents($radianUrl);
              $timeEntry = $radianDetails->time_entries[0];
              $insertTimeEntry = $this->RadianRepository->addTime($timeEntry->id,$inputUserData);
              if($insertTimeEntry){
                $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfuly created time entry');
              }else{
                $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'Failed to create time entry');
              }
              $jsonData = \Response::json($finalResult);
              return $jsonData;
        }
        public function getTimeEntries() {
               $inputUserData = \Input::all();
               $userId = !empty($inputUserData['user_id'])?$inputUserData['user_id']:'';
               $offset = !empty($inputUserData['offset'])?$inputUserData['offset']:0;
               $limit = !empty($inputUserData['limit'])?$inputUserData['limit']:25;
               $username = $inputUserData['username'];
               $password = $inputUserData['password'];
               $radianUrl = 'http://'.$username.':'.$password.'@radian.enterpi.com/redmine/time_entries.json?user_id='.$userId.'&offset='.$offset.'&limit='.$limit.'';
               $radianDetails = $this->urlFileContents($radianUrl);
               if($radianDetails->total_count != 0){
                foreach ($radianDetails->time_entries as $result){
                   $timeEntryDetails  = array();
                   $timeEntryDetails['id']= $result->id;
                   $timeEntryDetails['project']['id'] = $result->project->id;
                   $timeEntryDetails['project']['name'] = $result->project->name;
                   if(!empty($result->issue->id)){
                       $timeEntryDetails['issue']['id'] = $result->issue->id;
                       $IssuesUrl = 'http://'.$username.':'.$password.'@radian.enterpi.com/redmine/issues/'.$result->issue->id.'.json';
                        $issueDetails = $this->urlFileContents($IssuesUrl);
                        $timeEntryDetails['issue']['name'] = $issueDetails->issue->subject;
                   }
                   $timeEntryDetails['user']['id'] = $result->user->id;
                   $timeEntryDetails['user']['name'] = $result->user->name;
                   $timeEntryDetails['activity']['id'] = $result->activity->id;
                   $timeEntryDetails['activity']['name'] = $result->activity->name;
                   $timeEntryDetails['hours'] = $result->hours;
                   $timeEntryDetails['comments'] = $result->comments;
                   $timeEntryDetails['spent_on'] = $result->spent_on;
                   $timeEntryDetails['created_on'] = $result->created_on;
                   $timeEntryDetails['updated_on'] = $result->updated_on;
                   $sqlResult = $this->RadianRepository->getSartEndTime($result->id);
                   if(!empty($sqlResult)){
                       $timeEntryDetails['start_time'] = $sqlResult[0]->start_time;
                       $timeEntryDetails['end_time'] = $sqlResult[0]->end_time;
                   }else{
                        $timeEntryDetails['start_time'] = '';
                       $timeEntryDetails['end_time'] = '';
                   }
                   $returnData[] = $timeEntryDetails;
               }       
                   $data = array("time_entries" => array_values($returnData), "total_count" => $radianDetails->total_count, "limit" => $limit, "offset" => $offset);
                  $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly retrieved') + $data;
               }
               else{
                $data = array();
                 $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'No records found')+ $data;
               }
                $jsonData = \Response::json($finalResult);
                return $jsonData;
        }
        public function urlFileContents($url) {
            $json = file_get_contents($url);
            $data = json_decode($json);
            return $data;
            
        }
        
        public function projectsBySearch(){
            $inputUserData = \Input::all();
            $search = !empty($inputUserData['search'])?$inputUserData['search']:'';
            $limit = !empty($inputUserData['limit'])?$inputUserData['limit']:25;
            $offset = !empty($inputUserData['offset'])?$inputUserData['offset']:0;
            $totalList =  $this->RadianRepository->getAllProjects();
            $projectsList =  $this->RadianRepository->getProjects($search,$limit,$offset);
            if(!empty($projectsList)){
                foreach($projectsList as $res){
                   $projectDetails = array();
                   $projectDetails['id'] = (int)$res->project_id;
                   $projectDetails['name']  = $res->project_name;
                   $projectDetails['identifier']= $res->project_identifier;
                   $projectDetails['description']  = $res->project_description;
                   $projectDetails['status']  = $res->project_status;
                   $projectDetails['custom_fields'][0]['id']  = $res->cs_id;
                   $projectDetails['custom_fields'][0]['name']  = $res->cs_name;
                   $projectDetails['custom_fields'][0]['value']  = $res->cs_value;
                   $projectDetails['created_on']  = $res->created_on;
                   $projectDetails['updated_on']  = $res->updated_on;
                   $returnData[] = $projectDetails;

                }
                 $data = array("projects" => array_values($returnData), "total_count" => count($totalList), "limit" => $limit,"offset" => $offset);
                $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly retrieved') + $data;
                
            }else{
                $data = array();
                 $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'No records found')+ $data;
            }
            $jsonData = \Response::json($finalResult);
            return $jsonData;
        }
        
        public function editTime(){
              $body = array();
              $inputUserData = \Input::all();
              $inputUserData['issue_id']  = !empty($inputUserData['issue_id'])?$inputUserData['issue_id']:'';
              $body['time_entry']['issue_id'] = $inputUserData['issue_id'];
              $body['time_entry']['hours'] = $inputUserData['hours'];
              $body['time_entry']['activity_id'] = $inputUserData['activity_id'];
              $body['time_entry']['comments'] = $inputUserData['comments'];
              $body['time_entry']['spent_on'] = $inputUserData['spent_on'];
              $userName = $inputUserData['username'];
              $password = $inputUserData['password'];
              $url = 'http://'.$userName.':'.$password.'@radian.enterpi.com/redmine/time_entries/'.$inputUserData['id'].'.json';
              $post = curl_init();
              curl_setopt($post, CURLOPT_URL, $url);
              curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($post, CURLOPT_CUSTOMREQUEST, "PUT");
              curl_setopt($post, CURLOPT_POSTFIELDS,http_build_query($body));
              $result = curl_exec($post);
              curl_close($post);
               if(!empty($inputUserData)){
                 $updateTimeEntry = $this->RadianRepository->editTime($inputUserData);
                 if($updateTimeEntry){
                     $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfuly updated time entry');
                 }else{
                    $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'Failed to update time entry');
                }
                $jsonData = \Response::json($finalResult);
                return $jsonData;
              }
        }
        
        public function login(){
            $inputUserData = \Input::all();
            $username = $inputUserData['username']; 
            $password = $inputUserData['password']; 
            $userUrl = 'http://'.$username.':'.$password.'@radian.enterpi.com/redmine/users/current.json';
            $userDetails = $this->urlFileContents($userUrl);
            if(!empty($userDetails)){
            $user = array();    
            $user['id'] = $userDetails->user->id;
            $user['login'] = $userDetails->user->login;
            $user['firstname'] = $userDetails->user->firstname;
            $user['lastname'] = $userDetails->user->lastname;
            $user['mail'] = $userDetails->user->mail;
            $user['created_on'] = $userDetails->user->created_on;
            $user['last_login_on'] = $userDetails->user->last_login_on;
            $user['api_key'] = $userDetails->user->api_key;
            $user['status'] = (int)'1';
            $data = array("user" => $user);
            $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly logged in') + $data;
            }else{
                $data = array();
                $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'Failed to login')+ $data;
            }
            $jsonData = \Response::json($finalResult);
            return $jsonData;
        }
        
        
        public function getActivityList() {
           $resultSet = $this->RadianRepository->getActivities();
           foreach($resultSet as $res){
               $activities  = array();
               $activities['id'] = $res->activity_id;
               $activities['name'] = $res->activity_name;
               $returnData[]       = $activities;
           }
           if($returnData){
                $data = array("activity_list" => array_values($returnData));
                $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly retrieved') + $data;
                
            }else{
                $data = array();
                 $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'No records found')+ $data;
            }
            $jsonData = \Response::json($finalResult);
            return $jsonData;
        }
        
        public function deleteTime() {
            $inputUserData = \Input::all();
            $userName = $inputUserData['username'];
            $password = $inputUserData['password'];
            $client = new GuzzleHttpClient();
            $url = 'http://radian.enterpi.com/redmine/time_entries/'.$inputUserData['id'].'.json';
            $response = $client->request("DELETE", $url, ['auth' => [$userName,$password]]);
            if($response){
                $data = array();
                $finalResult = array('status_code' => '200', 'status' => 'Success', 'message' => 'Successfullly deleted') + $data;
                
            }else{
                $data = array();
                 $finalResult = array('status_code' => '403', 'status' => 'failure', 'message' => 'No records found')+ $data;
            }
            $jsonData = \Response::json($finalResult);
            return $jsonData;
        }
        
        
}


?>

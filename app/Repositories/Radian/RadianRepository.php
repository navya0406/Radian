<?php

namespace App\Repositories\Radian;

//use App\Repositories\Radian\RadianInterface as RadianInterface;
use DB;
class RadianRepository implements RadianInterface
{
    function __construct() {
    }
    public function getAllProjects() {
        $result = DB::table('projects')
                    ->select('id')
                    ->get();
        return $result;
    }
    public function getProjects($search='',$limit='',$offset=''){
        $sql = "select * from projects";
        if(!empty($search)){
            $sql.= " where project_name like '%".$search."%'";
        }
        $sql .= " limit $limit offset $offset";
        $result = DB::select($sql);
        return $result;
    }
    
    public function getIssues($projectId='',$search='',$limit='',$offset='') {
          $sql = "select * from issue_details where project_id='".$projectId."'";
            if(!empty($search)){
                  $sql.= " and (issue_id like '%".$search."%' or subject like '%".$search."%')";
            }
            if(empty($limit)){
                $limit = 25;
            }
            if(empty($offset)){
                $offset = 0;
            }
            $sql .= " limit $limit offset $offset";
            $result = DB::select($sql);
            return $result;
        
    }
    
    public function getAllIssues($projectId){
        $result = DB::table('issue_details')
                    ->select('id')
                    ->where('project_id','=',$projectId)
                    ->get();
        return $result;
    }
    
    public function getActivities() {
        $result = DB::table('activity_list')
                    ->select('activity_id','activity_name')
                    ->get();
        return $result;
    }

    public function editTime($inputUserData=array()) {
         $result = DB::table('issue_time_entry')
                   ->where('time_entry_id', $inputUserData['id'])  // find your user by their email
                   ->limit(1) 
                   ->update(array('start_time'=>$inputUserData['start_time'],
                                  'end_time'=>$inputUserData['end_time'],
                                  )
                          );
         return $result;
    }
    
    public function getSartEndTime($id='') {
        $result = DB::table('issue_time_entry')
                          ->where('time_entry_id', $id)
                           ->select('start_time','end_time')->get();
        return $result;
    }
    
    public function addTime($id='',$inputUserData=array()) {
        $result = DB::table('issue_time_entry')
                  ->insert(array('time_entry_id'=>$id,
                                 'start_time'=>$inputUserData['start_time'],
                                 'end_time'=>$inputUserData['end_time'],
                                 ));
        return $result;
        
    }
    
}

?>
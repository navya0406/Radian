<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
//use App\Http\Controllers\RadianController;
//use Illuminate\Foundation\IssuesInsertion;

class IssuesInsertion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insertIssues:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserting issues in database';
    protected $RadianController;
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function _construct(RadianController $RadianController){
        $this->RadianController = $RadianController;
    }
    public function handle()
    {   
            $project = DB::table('projects')
                                ->select('project_id') 
                                ->get();
            foreach($project as $p){
                $pId = $p->project_id;
                $radianUrl = 'http://p.inukonda:PRAVEEN@468@radian.enterpi.com/redmine/issues.json?project_id='.$pId.'';
                $radianDetails = $this->urlFileContents($radianUrl);
                $totalCount = $radianDetails->total_count;
                if($totalCount != 0){
                    for($offset = 0; $offset < $totalCount; $offset +=100){
                    $url = 'http://p.inukonda:PRAVEEN@468@radian.enterpi.com/redmine/issues.json?project_id='.$pId.'&offset='.$offset.'&limit=100';
                    $data = $this->urlFileContents($url);
                        foreach($data->issues as $ds){
                            $checkIssue = DB::table('issue_details')
                            ->select('id') 
                            ->where('issue_id', '=', $ds->id)
                             ->get();
                        if(empty($checkIssue)){
                            $issueId = $ds->id;
                            $projectId = !empty($ds->project->id)?$ds->project->id:'';
                            $projectName = !empty($ds->project->name)?$ds->project->name:'';
                            $trackerId = !empty($ds->tracker->id)?$ds->tracker->id:'';
                            $trackerName = !empty($ds->tracker->name)?$ds->tracker->name:'';
                            $statusId = !empty($ds->status->id)?$ds->status->id:'';
                            $statusName = !empty($ds->status->name)?$ds->status->name:'';
                            $priorityId = !empty($ds->priority->id)?$ds->priority->id:'';
                            $priorityName = !empty($ds->priority->name)?$ds->priority->name:'';
                            $authorId = !empty($ds->author->id)?$ds->author->id:'';
                            $authorName = !empty($ds->author->name)?$ds->author->name:'';
                            $assignedToId = !empty($ds->assigned_to->id)?$ds->assigned_to->id:'';
                            $assignedToName = !empty($ds->assigned_to->name)?$ds->assigned_to->name:'';
                            $fixedVersionId = !empty($ds->fixed_version->id)?$ds->fixed_version->id:'';
                            $fixedVersionName = !empty($ds->fixed_version->name)?$ds->fixed_version->name:'';
                            $subject = !empty($ds->subject)?$ds->subject:'';
                            $description = !empty($ds->description)?$ds->description:'';
                            $due_date = !empty($ds->due_date)?$ds->due_date:'';
                            $done_ratio = !empty($ds->done_ratio)?$ds->done_ratio:'';
                            $csid_1 = !empty($ds->custom_fields[0]->id)?$ds->custom_fields[0]->id:'';
                            $csname_1 = !empty($ds->custom_fields[0]->name)?$ds->custom_fields[0]->name:'';
                            if(isset($ds->custom_fields[0]->value)){
                            if(is_array($ds->custom_fields[0]->value) && !empty($ds->custom_fields[0]->value)){
                                $csvalue_1 = !empty($ds->custom_fields[0]->value[0])?$ds->custom_fields[0]->value[0]:'';
                            }else{
                                $csvalue_1 =!empty($ds->custom_fields[0]->value)?$ds->custom_fields[0]->value:'';
                            }
                            }else{
                                $csvalue_1= '0';
                            }
                            $csmultiple_1 = !empty($ds->custom_fields[0]->multiple)?$ds->custom_fields[0]->multiple:'';
                            $csid_2 = !empty($ds->custom_fields[1]->id)?$ds->custom_fields[1]->id:'';
                            $csname_2 = !empty($ds->custom_fields[1]->name)?$ds->custom_fields[1]->name:'';
                            $csvalue_2 = isset($ds->custom_fields[1]->value)?$ds->custom_fields[1]->value:'';
                            $csid_3 = !empty($ds->custom_fields[2]->id)?$ds->custom_fields[2]->id:'';
                            $csname_3 = !empty($ds->custom_fields[2]->name)?$ds->custom_fields[2]->name:'';
                            $csvalue_3 = isset($ds->custom_fields[2]->value)?$ds->custom_fields[2]->value:'';
                            $csid_4 = !empty($ds->custom_fields[3]->id)?$ds->custom_fields[3]->id:'';
                            $csname_4 = !empty($ds->custom_fields[3]->name)?$ds->custom_fields[3]->name:'';
                            $csvalue_4 = isset($ds->custom_fields[3]->value)?$ds->custom_fields[3]->value:'';
                            $csid_5 = !empty($ds->custom_fields[4]->id)?$ds->custom_fields[4]->id:'';
                            $csname_5 = !empty($ds->custom_fields[4]->name)?$ds->custom_fields[4]->name:'';
                            $csvalue_5 = isset($ds->custom_fields[4]->value)?$ds->custom_fields[4]->value:'';
                            $csid_6 = !empty($ds->custom_fields[5]->id)?$ds->custom_fields[5]->id:'';
                            $csname_6 = !empty($ds->custom_fields[5]->name)?$ds->custom_fields[5]->name:'';
                            $csvalue_6 = isset($ds->custom_fields[5]->value)?$ds->custom_fields[5]->value:'';
                            $csid_7 = !empty($ds->custom_fields[6]->id)?$ds->custom_fields[6]->id:'';
                            $csname_7 = !empty($ds->custom_fields[6]->name)?$ds->custom_fields[6]->name:'';
                            $csvalue_7 = isset($ds->custom_fields[6]->value)?$ds->custom_fields[6]->value:'';
                            $csid_8 = !empty($ds->custom_fields[7]->id)?$ds->custom_fields[7]->id:'';
                            $csname_8 = !empty($ds->custom_fields[7]->name)?$ds->custom_fields[7]->name:'';
                            $csvalue_8 = isset($ds->custom_fields[7]->value)?$ds->custom_fields[7]->value:'';
                            $createdOn = !empty($ds->created_on)?$ds->created_on:'';
                            $updatedOn = !empty($ds->updated_on)?$ds->updated_on:'';
                            $insertion = DB::table('issue_details')->insert(
                                         array('issue_id' => $issueId, 'project_id' => $projectId,
                                                'project_name'=>$projectName, 'tracker_id'=>$trackerId,
                                                'tracker_name'=>$trackerName, 'status_id'=>$statusId,
                                                'status_name'=>$statusName, 'priority_id'=>$priorityId,
                                                'priority_name'=>$priorityName,'author_id'=>$authorId,
                                                'author_name' =>$authorName, 'assigned_to_id'=>$assignedToId,
                                                'assigned_to_name'=>$assignedToName, 'fixed_version_id'=>$fixedVersionId,
                                                'fixed_version_name'=>$fixedVersionName, 'subject'=>$subject,
                                                'description'=>$description, 'due_date'=>$due_date,
                                                'done_ratio'=>$done_ratio, 'cs_id_1'=>$csid_1,
                                                'cs_name_1'=>$csname_1, 
                                             'cs_value_1'=>$csvalue_1,
                                             'cs_multiple_1'=>$csmultiple_1,
                                                'cs_id_2'=>$csid_2,'cs_name_2'=>$csname_2,
                                                'cs_value_2'=>$csvalue_2,
                                                'cs_id_3'=>$csid_3,
                                                'cs_name_3'=>$csname_3,
                                             'cs_value_3'=>$csvalue_3,
                                                'cs_id_4'=>$csid_4, 'cs_name_4'=>$csname_4,
                                                'cs_value_4'=>$csvalue_4,
                                             'cs_id_5'=>$csid_5, 
                                                'cs_value_4'=>$csvalue_4,'cs_id_5'=>$csid_5, 
                                                'cs_name_5'=>$csname_5, 
                                             'cs_value_5'=>$csvalue_5,
                                                'cs_id_6'=>$csid_6, 'cs_name_6'=>$csname_6, 
                                                'cs_value_6'=>$csvalue_6,
                                             'cs_id_7'=>$csid_7, 
                                                'cs_name_7'=>$csname_7,
                                             'cs_value_7'=>$csvalue_7,
                                                 'cs_id_8'=>$csid_8, 'cs_name_8'=>$csname_8,
                                             'cs_value_8'=>$csvalue_8,
                                                'created_on'=> $createdOn,'updated_on'=>$updatedOn)                                                               
                                            );
                        }
                        }
                    }
                }
                
            }
    }
    
    public function urlFileContents($url) {
            $json = file_get_contents($url);
            $data = json_decode($json);
            return $data;
            
        }
}
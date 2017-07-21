<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
//use Illuminate\Foundation\IssuesInsertion;

class ProjectsInsertion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insertProjects:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserting projects in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
            for($offset = 0; $offset <= 200; $offset +=100) {
            $projectsUrl = 'http://p.inukonda:PRAVEEN@468@radian.enterpi.com/redmine/projects.json?offset='.$offset.'&limit=100';
            $projectDetails = file_get_contents($projectsUrl);
            $project1 = json_decode($projectDetails);
            foreach ($project1->projects as $proj) {
               $checkProject = DB::table('projects')
                                ->select('id') 
                                ->where('project_id', '=', $proj->id)
                                ->get();
                if(empty($checkProject)){
                    $projectId = $proj->id;
                    $projectName = $proj->name;
                    $projectIdentifier = $proj->identifier;
                    $projectdescription = $proj->description;
                    $projectstatus = $proj->status;
                    $cs_id = $proj->custom_fields[0]->id;
                    $cs_name= $proj->custom_fields[0]->name;
                    $cs_value= $proj->custom_fields[0]->value;
                    $created_on= $proj->created_on;
                    $updated_on= $proj->updated_on;
                    $insertion = DB::table('projects')->insert(
                          array('project_id' => $projectId, 'project_name' => $projectName,
                           'project_identifier'=>$projectIdentifier, 'project_description'=>$projectdescription,
                           'project_status'=>$projectstatus, 'cs_id'=>$cs_id,
                           'cs_name'=>$cs_name, 'cs_value'=>$cs_value,
                            'created_on'=>$created_on,'updated_on'=>$updated_on));

                }
            }
            }
            
    }
}
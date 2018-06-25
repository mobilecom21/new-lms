<?php

namespace Assignment\Action;

use User\Model\User;
use Tutor\Model\TutorStudentCourseTable;
use Assignment\Model\AssignmentWorkTable;
use Zend\Expressive\Template;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Uploader\Uploader;
use ZipArchive;

class ResourcesByCourse
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Template\TemplateRendererInterface $template,
                                AssignmentWorkTable $assignmentWorkTable,
                                TutorStudentCourseTable $tutorStudentCourseTable,
                                Uploader $uploader)
    {
        $this->template = $template;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $userId = $request->getAttribute('userId');
        $courseId = $request->getAttribute('courseId');
        /**
         * @var User $currentUser
         */
        $currentUser = $request->getAttribute(User::class);
        $currentUserId = $currentUser->getId();
        $currentUserRole = $currentUser->getRole();
        if ('Rbac\Role\Tutor' == $currentUserRole && ! $this->tutorStudentCourseTable->isTutorForStudent($currentUserId, $userId)) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }
        $assignmentWorkResultSet = $this->assignmentWorkTable->byWorkerAndCourse($userId, $courseId);
        $tmp_work_zip = @tempnam ('tmp', uniqid( rand(), true));

        //a variable to hold infromation for a summary.txt document added to the zip at the end
        $summary = array();

        $work_zip = new ZipArchive();
        $work_files = 1;
        if (true === $work_zip->open( $tmp_work_zip ) ) {
            #throw new \Exception(var_dump($assignmentWorkResultSet));
            $counter = 0;
            $counterPassed = 0;
            foreach ($assignmentWorkResultSet as $assignmentWork) {
                $counter++;
                if( $assignmentWork->hasPassed() ) {
                    $counterPassed++;
                }
                $assignment = $this->uploader->download($assignmentWork->assignmentUploader()->getPath());

                if (! empty($assignment->getContent())) {

                    $worker = $assignmentWork->worker()->getArrayCopy();
                    $tutor = $assignmentWork->tutor()->getArrayCopy();

                    $summary[$assignmentWork->course()->getName()]['tutor']['name'] = $tutor['meta']['first_name'] . ' ' . $tutor['meta']['last_name'];
                    $summary[$assignmentWork->course()->getName()]['tutor']['username'] = $assignmentWork->tutor()->getUsername();
                    $summary[$assignmentWork->course()->getName()]['tutor']['email'] = $assignmentWork->tutor()->getIdentity();
                    $summary[$assignmentWork->course()->getName()]['tutor']['phone'] = $tutor['meta']['phone'];
                    $summary[$assignmentWork->course()->getName()]['student']['name'] = $worker['meta']['first_name'] . ' ' . $worker['meta']['last_name'];
                    $summary[$assignmentWork->course()->getName()]['student']['phone'] = $worker['meta']['phone'];
                    $summary[$assignmentWork->course()->getName()]['student']['address'] = $worker['meta']['address'];
                    $summary[$assignmentWork->course()->getName()]['student']['hln'] = $worker['courseTutor'][0]['HLN'];
                    //$summary[$assignmentWork->course()->getName()]['student']['start_of_support'] = date('d F Y', $worker['createdAt']);
                    


                    //get info about the uploaded file so we get the right file extension
                    $path_parts = pathinfo($assignmentWork->assignmentUploader()->getName());

                    //determine the status of the assignment
                    $passed = '';
                    if ($assignmentWork->isWaitingToPass()) {
                        $passed = 'Not Marked';
                    } else if ($assignmentWork->didNotPass()) {
                        $passed = 'Referred';
                    } else if ($assignmentWork->hasPassed()) {
                        $passed = 'Passed';
                    }

                    $created = $assignmentWork->createdAt();

                    //add the file with  the right name format
                    $name = $created->format('Ymd') . '-' .
                                $assignmentWork->assignment()->getName() . '-' . 
                                $passed . '.' . 
                                $path_parts['extension'];

                    $work_zip->addFromString(
                                $assignmentWork->course()->getName() . '/' .$name, 
                                $assignment->getContent()
                            );

                    $summary[$assignmentWork->course()->getName()]['submissions'][$created->format('U')]['name'] = $name;
                    $summary[$assignmentWork->course()->getName()]['submissions'][$created->format('U')]['originalname'] = $assignmentWork->assignmentUploader()->getName();

                    

                    $work_files++;
                    
                    //attach feedback if there is some
                    if (null !== $assignmentWork->feedbackUploader()) {
                        $feedback = $this->uploader->download($assignmentWork->feedbackUploader()->getPath());
                        $path_parts = pathinfo($assignmentWork->feedbackUploader()->getName());

                        $name = $created->format('Ymd') . '-' . 
                                $assignmentWork->assignment()->getName() . '-' . 
                                'Feedback.' . 
                                $path_parts['extension'];

                        $work_zip->addFromString(
                                $assignmentWork->course()->getName() . '/' .$name, 

                                $feedback->getContent()
                            );

                        $summary[$assignmentWork->course()->getName()]['submissions'][$assignmentWork->updatedAt()->format('U')]['name'] = $name;
                        $summary[$assignmentWork->course()->getName()]['submissions'][$assignmentWork->updatedAt()->format('U')]['originalname'] = $assignmentWork->assignmentUploader()->getName();

                        $work_files++;
                    }

                }
            }

            //create a summary file for auditors
            $text = '';
            foreach ($summary as $course => $item) {
                $text .= "STUDENT NAME:\t" . $item['student']['name'] . "\r\n";
                $text .= "STUDENT ADDRESS:\t" . $item['student']['address'] . "\r\n";
                $text .= "STUDENT PHONE:\t" . $item['student']['phone'] . "\r\n";
                //$text .= "STUDENT START OF SUPPORT: " . $item['student']['start_of_support'] . "\r\n";
                $text .= "STUDENT HLN:\t" . $item['student']['hln'] . "\r\n";
                
                $text .= "COURSE NAME:\t" . $course . "\r\n";
                
                $text .= "TUTOR NAME:\t" . $item['tutor']['name'] . "\r\n";
                $text .= "TUTOR EMAIL:\t" . $item['tutor']['email'] . "\r\n";
                $text .= "TUTOR PHONE:\t" . $item['tutor']['phone'] . "\r\n";
                $text .= "\r\n";
                
                $text .= "SUBMISSION SCHEDULE:\r\n-------------------\r\n\r\n";
                foreach($item['submissions'] as $ts => $data) {
                    $text .= date('d F Y', $ts) . ' :: ' . $data['name'] . "\r\n";
                }

                #$text .= "\r\n\r\n";
                #$text .= "MODULES COMPLETED: {$counterPassed} OF {$counter}";
            }

            $work_zip->addFromString(
                $assignmentWork->course()->getName() . '/summary.txt', 
                $text
            );

            #throw new \Exception(var_dump($text));
        }
        $work_zip->close();

        return new HtmlResponse(
            file_get_contents($tmp_work_zip),
            '200',
            [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Content-type' => 'application/zip',
                'Content-Length' => filesize($tmp_work_zip),
                'Content-Disposition' => 'attachment; filename=All_Submissions_For_User_' . $userId . '.zip',
            ]
        );
    }
}
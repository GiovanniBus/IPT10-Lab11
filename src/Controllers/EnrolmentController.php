<?php

namespace App\Controllers;

use App\Models\Course;
use App\Models\CourseEnrolment;
use App\Models\Student;

class EnrolmentController extends BaseController
{
    public function enrollmentForm()
    {
        $template = 'enrollment-form';
        $data = [
            'courses' => (new Course())->all(),
            'students' => (new Student())->all()
        ];

        return $this->render($template, $data);
    }

    public function enroll()
    {
        $enrolmentModel = new CourseEnrolment();
        $enrolmentModel->enroll(
            $_POST['course_code'],
            $_POST['student_code'],
            $_POST['enrollment_date']
        );

        // Redirect to the course page after enrollment
        header("Location: /courses/{$_POST['course_code']}");
        exit();
    }
}

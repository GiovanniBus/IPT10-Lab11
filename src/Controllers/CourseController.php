<?php

namespace App\Controllers;

use App\Models\Course;
use Fpdf\Fpdf;

class CourseController extends BaseController
{
    public function list()
    {
        $courses = (new Course())->all();
        return $this->render('courses', ['items' => $courses]);
    }

    public function viewCourse($course_code)
    {
        $courseModel = new Course();
        return $this->render('single-course', [
            'course' => $courseModel->find($course_code),
            'enrollees' => $courseModel->getEnrolees($course_code)
        ]);
    }

    public function exportPDF($course_code)
    {
        $courseModel = new Course();
        $course = $courseModel->find($course_code);
        $enrollees = $courseModel->getEnrolees($course_code);

        $pdf = new FPDF();
        $pdf->AddPage();

        // Title
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 10, 'Course Information', 0, 1, 'C');

        // Separator
        $pdf->SetDrawColor(0, 51, 102);
        $pdf->Line(10, 20, 200, 20);
        $pdf->Ln(10);

        // Course details
        $this->addCourseDetails($pdf, $course);

        // List of enrollees
        $this->addEnrolleesList($pdf, $enrollees);
        
        // Output the PDF
        $pdf->Output('D', 'course_' . $course_code . '_enrollees.pdf');
    }

    private function addCourseDetails($pdf, $course)
    {
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Cell(50, 10, 'Course Code:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->course_code, 0, 1, 'L');

        $pdf->Cell(50, 10, 'Course Name:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->course_name, 0, 1, 'L');

        $pdf->Cell(50, 10, 'Description:', 0, 0, 'L');
        $pdf->MultiCell(0, 10, $course->description, 0, 'L');

        $pdf->Cell(50, 10, 'Credits:', 0, 0, 'L');
        $pdf->Cell(0, 10, $course->credits, 0, 1, 'L');

        $pdf->Ln(10);
    }

    private function addEnrolleesList($pdf, $enrollees)
    {
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 10, 'List of Enrollees', 0, 1, 'C');
        $pdf->Ln(5);

        $columnWidths = [
            'ID' => 20,
            'First Name' => 40,
            'Last Name' => 40,
            'Email' => 60,
            'Date of Birth' => 30,
            'Sex' => 20,
        ];

        $this->setEnrolleeTableHeader($pdf, $columnWidths);
        $this->addEnrolleesData($pdf, $enrollees, $columnWidths);
    }

    private function setEnrolleeTableHeader($pdf, $columnWidths)
    {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(100, 149, 237);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->SetX(($pdf->GetPageWidth() - array_sum($columnWidths)) / 2);
        foreach ($columnWidths as $colName => $width) {
            $pdf->Cell($width, 10, $colName, 1, 0, 'C', true);
        }
        $pdf->Ln();
    }

    private function addEnrolleesData($pdf, $enrollees, $columnWidths)
    {
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        
        if (!empty($enrollees)) {
            foreach ($enrollees as $enrollee) {
                $pdf->SetX(($pdf->GetPageWidth() - array_sum($columnWidths)) / 2);
                $pdf->Cell($columnWidths['ID'], 10, $enrollee["student_code"], 1);
                $pdf->Cell($columnWidths['First Name'], 10, $enrollee["first_name"], 1);
                $pdf->Cell($columnWidths['Last Name'], 10, $enrollee["last_name"], 1);
                $pdf->Cell($columnWidths['Email'], 10, $enrollee["email"], 1);
                $pdf->Cell($columnWidths['Date of Birth'], 10, $enrollee["date_of_birth"], 1);
                $pdf->Cell($columnWidths['Sex'], 10, $enrollee["sex"], 1);
                $pdf->Ln();
            }
        } else {
            $pdf->SetX(($pdf->GetPageWidth() - array_sum($columnWidths)) / 2);
            $pdf->Cell(array_sum($columnWidths), 10, 'No enrollees found for this course.', 1, 1, 'C');
        }
    }
}

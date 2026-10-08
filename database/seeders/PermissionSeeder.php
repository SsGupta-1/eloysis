<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],

            // Admin Users / Staffs
            ['name' => 'View Admin Users', 'slug' => 'admins.view', 'module' => 'admins'],
            ['name' => 'Create Admin Users', 'slug' => 'admins.create', 'module' => 'admins'],
            ['name' => 'Edit Admin Users', 'slug' => 'admins.edit', 'module' => 'admins'],
            ['name' => 'Delete Admin Users', 'slug' => 'admins.delete', 'module' => 'admins'],
            ['name' => 'Manage Admin Permissions', 'slug' => 'admins.permissions', 'module' => 'admins'],

            // Roles
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'roles'],
            ['name' => 'Manage Role Permissions', 'slug' => 'roles.permissions', 'module' => 'roles'],

            // Academic Management
            ['name' => 'View Academic Sessions', 'slug' => 'academic_sessions.view', 'module' => 'academic_sessions'],
            ['name' => 'Create Academic Sessions', 'slug' => 'academic_sessions.create', 'module' => 'academic_sessions'],
            ['name' => 'Edit Academic Sessions', 'slug' => 'academic_sessions.edit', 'module' => 'academic_sessions'],
            ['name' => 'Delete Academic Sessions', 'slug' => 'academic_sessions.delete', 'module' => 'academic_sessions'],

            ['name' => 'View Classes', 'slug' => 'classes.view', 'module' => 'classes'],
            ['name' => 'Create Classes', 'slug' => 'classes.create', 'module' => 'classes'],
            ['name' => 'Edit Classes', 'slug' => 'classes.edit', 'module' => 'classes'],
            ['name' => 'Delete Classes', 'slug' => 'classes.delete', 'module' => 'classes'],

            ['name' => 'View Sections', 'slug' => 'sections.view', 'module' => 'sections'],
            ['name' => 'Create Sections', 'slug' => 'sections.create', 'module' => 'sections'],
            ['name' => 'Edit Sections', 'slug' => 'sections.edit', 'module' => 'sections'],
            ['name' => 'Delete Sections', 'slug' => 'sections.delete', 'module' => 'sections'],

            ['name' => 'View Class Sections', 'slug' => 'class_sections.view', 'module' => 'class_sections'],
            ['name' => 'Create Class Sections', 'slug' => 'class_sections.create', 'module' => 'class_sections'],
            ['name' => 'Delete Class Sections', 'slug' => 'class_sections.delete', 'module' => 'class_sections'],

            ['name' => 'View Subjects', 'slug' => 'subjects.view', 'module' => 'subjects'],
            ['name' => 'Create Subjects', 'slug' => 'subjects.create', 'module' => 'subjects'],
            ['name' => 'Edit Subjects', 'slug' => 'subjects.edit', 'module' => 'subjects'],
            ['name' => 'Delete Subjects', 'slug' => 'subjects.delete', 'module' => 'subjects'],

            ['name' => 'View Class Subjects', 'slug' => 'class_subjects.view', 'module' => 'class_subjects'],
            ['name' => 'Create Class Subjects', 'slug' => 'class_subjects.create', 'module' => 'class_subjects'],
            ['name' => 'Delete Class Subjects', 'slug' => 'class_subjects.delete', 'module' => 'class_subjects'],

            ['name' => 'View Teachers', 'slug' => 'teachers.view', 'module' => 'teachers'],
            ['name' => 'Create Teachers', 'slug' => 'teachers.create', 'module' => 'teachers'],
            ['name' => 'Edit Teachers', 'slug' => 'teachers.edit', 'module' => 'teachers'],
            ['name' => 'Delete Teachers', 'slug' => 'teachers.delete', 'module' => 'teachers'],
            ['name' => 'Manage Teacher Permissions', 'slug' => 'teachers.permissions', 'module' => 'teachers'],

            ['name' => 'View Teacher Subjects', 'slug' => 'teacher_subjects.view', 'module' => 'teacher_subjects'],
            ['name' => 'Assign Teacher Subjects', 'slug' => 'teacher_subjects.create', 'module' => 'teacher_subjects'],
            ['name' => 'Delete Teacher Subjects', 'slug' => 'teacher_subjects.delete', 'module' => 'teacher_subjects'],

            ['name' => 'View Teacher Attendance', 'slug' => 'teacher_attendance.view', 'module' => 'teacher_attendance'],
            ['name' => 'Mark Teacher Attendance', 'slug' => 'teacher_attendance.create', 'module' => 'teacher_attendance'],

            ['name' => 'View Academic Periods', 'slug' => 'periods.view', 'module' => 'periods'],
            ['name' => 'Create Academic Periods', 'slug' => 'periods.create', 'module' => 'periods'],
            ['name' => 'Edit Academic Periods', 'slug' => 'periods.edit', 'module' => 'periods'],
            ['name' => 'Delete Academic Periods', 'slug' => 'periods.delete', 'module' => 'periods'],

            ['name' => 'View Class Timetable', 'slug' => 'class_timetables.view', 'module' => 'class_timetables'],
            ['name' => 'Create Class Timetable', 'slug' => 'class_timetables.create', 'module' => 'class_timetables'],
            ['name' => 'Edit Class Timetable', 'slug' => 'class_timetables.edit', 'module' => 'class_timetables'],
            ['name' => 'Delete Class Timetable', 'slug' => 'class_timetables.delete', 'module' => 'class_timetables'],

            // Student Management
            ['name' => 'View Students', 'slug' => 'students.view', 'module' => 'students'],
            ['name' => 'Create Students', 'slug' => 'students.create', 'module' => 'students'],
            ['name' => 'Edit Students', 'slug' => 'students.edit', 'module' => 'students'],
            ['name' => 'Delete Students', 'slug' => 'students.delete', 'module' => 'students'],

            ['name' => 'View Student Promotions', 'slug' => 'student_promotions.view', 'module' => 'student_promotions'],
            ['name' => 'Promote Students', 'slug' => 'student_promotions.create', 'module' => 'student_promotions'],

            ['name' => 'View Student Attendance', 'slug' => 'attendance.view', 'module' => 'attendance'],
            ['name' => 'Mark Student Attendance', 'slug' => 'attendance.create', 'module' => 'attendance'],

            ['name' => 'View Admission Enquiries', 'slug' => 'admission_enquiry.view', 'module' => 'admission_enquiry'],
            ['name' => 'Create Admission Enquiries', 'slug' => 'admission_enquiry.create', 'module' => 'admission_enquiry'],
            ['name' => 'Edit Admission Enquiries', 'slug' => 'admission_enquiry.edit', 'module' => 'admission_enquiry'],
            ['name' => 'Delete Admission Enquiries', 'slug' => 'admission_enquiry.delete', 'module' => 'admission_enquiry'],
            ['name' => 'Add Enquiry Followup', 'slug' => 'admission_enquiry.followup', 'module' => 'admission_enquiry'],
            ['name' => 'Convert Enquiry to Student', 'slug' => 'admission_enquiry.convert', 'module' => 'admission_enquiry'],

            // Examinations & Question Paper
            ['name' => 'View Questions', 'slug' => 'questions.view', 'module' => 'questions'],
            ['name' => 'Create Questions', 'slug' => 'questions.create', 'module' => 'questions'],
            ['name' => 'Edit Questions', 'slug' => 'questions.edit', 'module' => 'questions'],
            ['name' => 'Delete Questions', 'slug' => 'questions.delete', 'module' => 'questions'],

            ['name' => 'View Question Papers', 'slug' => 'question_papers.view', 'module' => 'question_papers'],
            ['name' => 'Create Question Papers', 'slug' => 'question_papers.create', 'module' => 'question_papers'],
            ['name' => 'Edit Question Papers', 'slug' => 'question_papers.edit', 'module' => 'question_papers'],
            ['name' => 'Delete Question Papers', 'slug' => 'question_papers.delete', 'module' => 'question_papers'],
            ['name' => 'Lock/Unlock Question Papers', 'slug' => 'question_papers.lock', 'module' => 'question_papers'],
            ['name' => 'Approve Question Papers', 'slug' => 'question_papers.approve', 'module' => 'question_papers'],
            ['name' => 'Print Question Papers', 'slug' => 'question_papers.print', 'module' => 'question_papers'],

            ['name' => 'View Exams', 'slug' => 'exams.view', 'module' => 'exams'],
            ['name' => 'Create Exams', 'slug' => 'exams.create', 'module' => 'exams'],
            ['name' => 'Edit Exams', 'slug' => 'exams.edit', 'module' => 'exams'],
            ['name' => 'Delete Exams', 'slug' => 'exams.delete', 'module' => 'exams'],
            ['name' => 'Exam Marks Entry', 'slug' => 'exams.marks_entry', 'module' => 'exams'],
            ['name' => 'Print Admit Cards', 'slug' => 'exams.admit_cards', 'module' => 'exams'],

            ['name' => 'View Exam Results', 'slug' => 'results.view', 'module' => 'results'],
            ['name' => 'Publish Exam Results', 'slug' => 'results.publish', 'module' => 'results'],
            ['name' => 'Export / Print Results', 'slug' => 'results.export', 'module' => 'results'],

            // Fee Management
            ['name' => 'Collect Student Fees', 'slug' => 'fees.payments.collect', 'module' => 'fees'],
            ['name' => 'View Fee Transactions', 'slug' => 'fees.payments.view', 'module' => 'fees'],
            ['name' => 'Cancel Fee Payment', 'slug' => 'fees.payments.cancel', 'module' => 'fees'],
            ['name' => 'Print Fee Receipt', 'slug' => 'fees.payments.print', 'module' => 'fees'],

            ['name' => 'View Fee Allocations', 'slug' => 'fees.allocations.view', 'module' => 'fees'],
            ['name' => 'Allocate Fees to Students', 'slug' => 'fees.allocations.create', 'module' => 'fees'],
            ['name' => 'Delete Fee Allocations', 'slug' => 'fees.allocations.delete', 'module' => 'fees'],

            ['name' => 'View Fee Structures', 'slug' => 'fees.structures.view', 'module' => 'fees'],
            ['name' => 'Create Fee Structures', 'slug' => 'fees.structures.create', 'module' => 'fees'],
            ['name' => 'Edit Fee Structures', 'slug' => 'fees.structures.edit', 'module' => 'fees'],
            ['name' => 'Delete Fee Structures', 'slug' => 'fees.structures.delete', 'module' => 'fees'],

            ['name' => 'View Fee Heads', 'slug' => 'fees.heads.view', 'module' => 'fees'],
            ['name' => 'Create Fee Heads', 'slug' => 'fees.heads.create', 'module' => 'fees'],
            ['name' => 'Edit Fee Heads', 'slug' => 'fees.heads.edit', 'module' => 'fees'],
            ['name' => 'Delete Fee Heads', 'slug' => 'fees.heads.delete', 'module' => 'fees'],

            ['name' => 'View Fee Discounts', 'slug' => 'fees.discounts.view', 'module' => 'fees'],
            ['name' => 'Create Fee Discounts', 'slug' => 'fees.discounts.create', 'module' => 'fees'],
            ['name' => 'Edit Fee Discounts', 'slug' => 'fees.discounts.edit', 'module' => 'fees'],
            ['name' => 'Delete Fee Discounts', 'slug' => 'fees.discounts.delete', 'module' => 'fees'],

            // Website Management
            ['name' => 'Manage Home Page Builder', 'slug' => 'website.homepage_builder', 'module' => 'website'],
            ['name' => 'View Home Sliders', 'slug' => 'website.sliders.view', 'module' => 'website'],
            ['name' => 'Manage Home Sliders', 'slug' => 'website.sliders.manage', 'module' => 'website'],
            ['name' => 'Manage Important Messages', 'slug' => 'website.messages.manage', 'module' => 'website'],
            ['name' => 'Manage Announcements', 'slug' => 'website.announcements.manage', 'module' => 'website'],
            ['name' => 'Manage Quick Links', 'slug' => 'website.quick_links.manage', 'module' => 'website'],
            ['name' => 'Manage Testimonials', 'slug' => 'website.testimonials.manage', 'module' => 'website'],
            ['name' => 'Manage News & Notices', 'slug' => 'website.news.manage', 'module' => 'website'],
            ['name' => 'Manage Events', 'slug' => 'website.events.manage', 'module' => 'website'],
            ['name' => 'Manage Photo Gallery', 'slug' => 'website.gallery.manage', 'module' => 'website'],
            ['name' => 'Manage Contact Messages', 'slug' => 'website.contact_messages.manage', 'module' => 'website'],
            ['name' => 'Manage Website Settings', 'slug' => 'website.settings.manage', 'module' => 'website'],

            // Logs & Settings
            ['name' => 'View System Logs', 'slug' => 'logs.view', 'module' => 'logs'],
            ['name' => 'Download System Logs', 'slug' => 'logs.download', 'module' => 'logs'],
            ['name' => 'View Activity Audit Logs', 'slug' => 'activity_logs.view', 'module' => 'activity_logs'],
            ['name' => 'Delete Activity Audit Logs', 'slug' => 'activity_logs.delete', 'module' => 'activity_logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'status' => 1,
                    'description' => null,
                ]
            );
        }
    }
}

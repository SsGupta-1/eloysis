<aside class="admin-sidebar" id="adminSidebar">

    {{-- User --}}
    <div class="sidebar-user-card">

        <div class="user-card-banner">

            <div class="user-avatar-wrapper">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name) }}&background=0284C7&color=fff"
                    alt="{{ auth('admin')->user()->name }}"
                    class="user-avatar-img">

            </div>

        </div>

        <div class="user-card-body">

            <h6 class="user-name">
                {{ auth('admin')->user()->name }}
            </h6>

            <small class="user-role">
                {{ ucfirst(auth('admin')->user()->role->role_name ?? 'Admin') }}
            </small>

        </div>

    </div>


    {{-- Navigation --}}
    <nav class="sidebar-menu">

        <ul>

            <li class="menu-title">
                NAVIGATION
            </li>


            {{-- Dashboard --}}
            @hasPermission('dashboard.view')
            <x-admin.menu-item
                route="admin.dashboard"
                icon="bi bi-speedometer2"
                label="Dashboard"
            />
            @endhasPermission

            {{-- Academic Management --}}
            @hasAnyPermission('academic_sessions.view|academic_sessions.create|classes.view|classes.create|sections.view|sections.create|class_sections.view|class_sections.create|subjects.view|subjects.create|class_subjects.view|class_subjects.create|teacher_subjects.view|teacher_subjects.create|teacher_attendance.view|teacher_attendance.create|periods.view|periods.create|class_timetables.view|class_timetables.create')
            <x-admin.menu-group
                id="academicMenu"
                title="Academic Management"
                icon="bi bi-mortarboard"
                :active="menu_active('academic')">

                @hasAnyPermission('academic_sessions.view|academic_sessions.create')
                <x-admin.menu-item
                    route="admin.academic.index"
                    icon="bi bi-calendar3"
                    label="Academic Session"
                />
                @endhasAnyPermission

                @hasAnyPermission('classes.view|classes.create')
                <x-admin.menu-item
                    route="admin.classes.index"
                    active="admin.classes.*"
                    icon="bi bi-building"
                    label="Classes"
                />
                @endhasAnyPermission

                @hasAnyPermission('sections.view|sections.create')
                <x-admin.menu-item
                    route="admin.sections.index"
                    active="admin.sections.*"
                    icon="bi bi-diagram-3"
                    label="Sections"
                />
                @endhasAnyPermission

                @hasAnyPermission('class_sections.view|class_sections.create')
                <x-admin.menu-item
                    route="admin.class-sections.index"
                    active="admin.class-sections.*"
                    icon="bi bi-diagram-2"
                    label="Class Sections"
                />
                @endhasAnyPermission

                @hasAnyPermission('subjects.view|subjects.create')
                <x-admin.menu-item
                    route="admin.subjects.index"
                    active="admin.subjects.*"
                    icon="bi bi-book"
                    label="Subjects"
                />
                @endhasAnyPermission

                @hasAnyPermission('class_subjects.view|class_subjects.create')
                <x-admin.menu-item
                    route="admin.clsubject.index"
                    active="admin.clsubject.*"
                    icon="bi bi-journal-bookmark"
                    label="Class Subjects"
                />
                @endhasAnyPermission

                @hasAnyPermission('teacher_subjects.view|teacher_subjects.create')
                <x-admin.menu-item
                    route="admin.teacher-subject.index"
                    active="admin.teacher-subject.*"
                    icon="bi bi-person-video2"
                    label="Teacher Subjects"
                />
                @endhasAnyPermission

                @hasAnyPermission('teacher_attendance.view|teacher_attendance.create')
                <x-admin.menu-item
                    route="admin.teacher_attendance.index"
                    active="admin.teacher_attendance.*"
                    icon="bi bi-calendar-check"
                    label="Teacher Attendance"
                />
                @endhasAnyPermission

                @hasAnyPermission('periods.view|periods.create')
                <x-admin.menu-item
                    route="admin.periods.index"
                    active="admin.periods.*"
                    icon="bi bi-calendar-week"
                    label="Academic Periods"
                />
                @endhasAnyPermission

                @hasAnyPermission('class_timetables.view|class_timetables.create')
                <x-admin.menu-item
                    route="admin.class-timetables.index"
                    active="admin.class-timetables.*"
                    icon="bi bi-clock-history"
                    label="Class Timetable"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission


            {{-- Students --}}
            @hasAnyPermission('students.view|students.create|student_promotions.view|student_promotions.create|attendance.view|attendance.create|admission_enquiry.view|admission_enquiry.create')
            <x-admin.menu-group
                id="studentMenu"
                title="Students Management"
                icon="bi bi-people"
                :active="menu_active('students')">

                @hasAnyPermission('students.view|students.create')
                <x-admin.menu-item
                    route="admin.students.index"
                    active="admin.students.*"
                    icon="bi bi-list-ul"
                    label="Student List"
                />
                @endhasAnyPermission

                @hasAnyPermission('student_promotions.view|student_promotions.create')
                <x-admin.menu-item
                    route="admin.student-promotions.index"
                    active="admin.student-promotions.*"
                    icon="bi bi-mortarboard-fill"
                    label="Student Promotion"
                />
                @endhasAnyPermission

                @hasAnyPermission('attendance.view|attendance.create')
                <x-admin.menu-item
                    route="admin.attendance.index"
                    active="admin.attendance.*"
                    icon="bi bi-calendar-check"
                    label="Attendance"
                />
                @endhasAnyPermission

                @hasAnyPermission('admission_enquiry.view|admission_enquiry.create')
                <x-admin.menu-item
                    route="admin.admission-enquiry.index"
                    active="admin.admission-enquiry.*"
                    icon="bi bi-person-lines-fill"
                    label="Admission Enquiry"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission


            {{-- Examinations --}}
            @hasAnyPermission('questions.view|questions.create|question_papers.view|question_papers.create|exams.view|exams.create|results.view')
            <x-admin.menu-group
                id="examMenu"
                title="Examinations"
                icon="bi bi-journal-check"
                :active="menu_active('examinations')">

                @hasAnyPermission('questions.view|questions.create')
                <x-admin.menu-item
                    route="admin.questions.index"
                    active="admin.questions.*"
                    icon="bi bi-patch-question"
                    label="Question Bank"
                />
                @endhasAnyPermission

                @hasAnyPermission('question_papers.view|question_papers.create')
                <x-admin.menu-item
                    route="admin.question-papers.index"
                    active="admin.question-papers.*"
                    icon="bi bi-file-earmark-text"
                    label="Question Papers"
                />
                @endhasAnyPermission

                @hasAnyPermission('exams.view|exams.create')
                <x-admin.menu-item
                    route="admin.exams.index"
                    active="admin.exams.*"
                    icon="bi bi-journal-text"
                    label="Exams"
                />
                @endhasAnyPermission

                @hasAnyPermission('results.view|results.publish|results.export')
                <x-admin.menu-item
                    route="admin.results.index"
                    active="admin.results.*"
                    icon="bi bi-award"
                    label="Results"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission


            {{-- Fee Management --}}
            @hasAnyPermission('fees.payments.collect|fees.payments.view|fees.allocations.view|fees.allocations.create|fees.structures.view|fees.structures.create|fees.heads.view|fees.heads.create|fees.discounts.view|fees.discounts.create')
            <x-admin.menu-group
                id="feeMenu"
                title="Fee Management"
                icon="bi bi-cash-stack"
                :active="menu_active('fees')">

                @hasPermission('fees.payments.collect')
                <x-admin.menu-item
                    route="admin.fees.payments.collect"
                    active="admin.fees.payments.collect"
                    icon="bi bi-wallet2"
                    label="Collect Fees (POS)"
                />
                @endhasPermission

                @hasAnyPermission('fees.payments.view|fees.payments.cancel|fees.payments.print')
                <x-admin.menu-item
                    route="admin.fees.payments.index"
                    active="admin.fees.payments.index"
                    icon="bi bi-receipt"
                    label="Fee Transactions"
                />
                @endhasAnyPermission

                @hasAnyPermission('fees.allocations.view|fees.allocations.create|fees.allocations.delete')
                <x-admin.menu-item
                    route="admin.fees.allocations.index"
                    active="admin.fees.allocations.*"
                    icon="bi bi-person-check"
                    label="Student Allocations"
                />
                @endhasAnyPermission

                @hasAnyPermission('fees.structures.view|fees.structures.create|fees.structures.edit|fees.structures.delete')
                <x-admin.menu-item
                    route="admin.fees.structures.index"
                    active="admin.fees.structures.*"
                    icon="bi bi-calculator"
                    label="Fee Structures"
                />
                @endhasAnyPermission

                @hasAnyPermission('fees.heads.view|fees.heads.create|fees.heads.edit|fees.heads.delete')
                <x-admin.menu-item
                    route="admin.fees.heads.index"
                    active="admin.fees.heads.*"
                    icon="bi bi-tag"
                    label="Fee Heads"
                />
                @endhasAnyPermission

                @hasAnyPermission('fees.discounts.view|fees.discounts.create|fees.discounts.edit|fees.discounts.delete')
                <x-admin.menu-item
                    route="admin.fees.discounts.index"
                    active="admin.fees.discounts.*"
                    icon="bi bi-percent"
                    label="Discounts & Rules"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission


            {{-- Website Management --}}
            @hasAnyPermission('website.homepage_builder|website.sliders.view|website.sliders.manage|website.messages.manage|website.announcements.manage|website.quick_links.manage|website.testimonials.manage|website.news.manage|website.events.manage|website.gallery.manage|website.contact_messages.manage|website.settings.manage')
            <x-admin.menu-group
                id="websiteMenu"
                title="Website Management"
                icon="bi bi-globe"
                :active="menu_active('website')">

                @hasPermission('website.homepage_builder')
                <x-admin.menu-item
                    route="admin.homepage-builder.index"
                    active="admin.homepage-builder.*"
                    icon="bi bi-layout-text-window-reverse"
                    label="Home Page Builder"
                />
                @endhasPermission

                @hasAnyPermission('website.sliders.view|website.sliders.manage')
                <x-admin.menu-item
                    route="admin.home-slider.index"
                    active="admin.home-slider.*"
                    icon="bi bi-images"
                    label="Home Sliders"
                />
                @endhasAnyPermission

                @hasPermission('website.messages.manage')
                <x-admin.menu-item
                    route="admin.important-messages.index"
                    active="admin.important-messages.*"
                    icon="bi bi-exclamation-octagon"
                    label="Important Messages"
                />
                @endhasPermission

                @hasPermission('website.announcements.manage')
                <x-admin.menu-item
                    route="admin.announcements.index"
                    active="admin.announcements.*"
                    icon="bi bi-megaphone"
                    label="Announcements"
                />
                @endhasPermission

                @hasPermission('website.quick_links.manage')
                <x-admin.menu-item
                    route="admin.quick-links.index"
                    active="admin.quick-links.*"
                    icon="bi bi-link-45deg"
                    label="Quick Links"
                />
                @endhasPermission

                @hasPermission('website.testimonials.manage')
                <x-admin.menu-item
                    route="admin.testimonials.index"
                    active="admin.testimonials.*"
                    icon="bi bi-chat-quote"
                    label="Testimonials"
                />
                @endhasPermission

                @hasPermission('website.news.manage')
                <x-admin.menu-item
                    route="admin.news.index"
                    active="admin.news.*"
                    icon="bi bi-newspaper"
                    label="News & Notices"
                />
                @endhasPermission

                @hasPermission('website.events.manage')
                <x-admin.menu-item
                    route="admin.events.index"
                    active="admin.events.*"
                    icon="bi bi-calendar-event"
                    label="Events"
                />
                @endhasPermission

                @hasPermission('website.gallery.manage')
                <x-admin.menu-item
                    route="admin.gallery.index"
                    active="admin.gallery.*"
                    icon="bi bi-camera"
                    label="Photo Gallery"
                />
                @endhasPermission

                @hasPermission('website.contact_messages.manage')
                <x-admin.menu-item
                    route="admin.contact-messages.index"
                    active="admin.contact-messages.*"
                    icon="bi bi-envelope"
                    label="Contact Messages"
                />
                @endhasPermission

                @hasPermission('website.settings.manage')
                <x-admin.menu-item
                    route="admin.website-settings.index"
                    active="admin.website-settings.*"
                    icon="bi bi-sliders"
                    label="Website Settings"
                />
                @endhasPermission

            </x-admin.menu-group>
            @endhasAnyPermission

            {{-- Administration --}}
            @hasAnyPermission('roles.view|roles.create|admins.view|admins.create|teachers.view|teachers.create')
            <li class="menu-title">
                ADMINISTRATION
            </li>

            {{-- User Management --}}
            <x-admin.menu-group
                id="userMenu"
                title="User Management"
                icon="bi bi-shield-lock"
                :active="menu_active('users')">

                @hasAnyPermission('roles.view|roles.create|roles.edit|roles.delete')
                <x-admin.menu-item
                    route="admin.roles.index"
                    active="admin.roles.*"
                    icon="bi bi-person-badge"
                    label="Roles"
                />
                @endhasAnyPermission

                @hasAnyPermission('admins.view|admins.create|admins.edit|admins.delete')
                <x-admin.menu-item
                    route="admin.staffs.index"
                    active="admin.staffs.*"
                    icon="bi bi-person-gear"
                    label="Admin Users"
                />
                @endhasAnyPermission

                @hasAnyPermission('teachers.view|teachers.create')
                <x-admin.menu-item
                    route="admin.teachers.index"
                    active="admin.teachers.*"
                    icon="bi bi-person-workspace"
                    label="Teachers"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission


            {{-- Settings & Logs --}}
            @hasAnyPermission('logs.view|logs.download|activity_logs.view')
            <x-admin.menu-group
                id="settingsMenu"
                title="Logs & Audit"
                icon="bi bi-shield-check"
                :active="menu_active('settings') || request()->routeIs('admin.activity-logs.*') || request()->routeIs('admin.logs.*')">

                @hasPermission('activity_logs.view')
                <x-admin.menu-item
                    route="admin.activity-logs.index"
                    active="admin.activity-logs.*"
                    icon="bi bi-clock-history"
                    label="Activity Audit Logs"
                />
                @endhasPermission

                @hasAnyPermission('logs.view|logs.download')
                <x-admin.menu-item
                    route="admin.logs.index"
                    active="admin.logs.*"
                    icon="bi bi-file-earmark-text"
                    label="Daily Log Files"
                />
                @endhasAnyPermission

            </x-admin.menu-group>
            @endhasAnyPermission

        </ul>

    </nav>

</aside>
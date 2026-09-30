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
            <x-admin.menu-item
                route="admin.dashboard"
                icon="bi bi-speedometer2"
                label="Dashboard"
            />


            {{-- Website --}}
            <x-admin.menu-group
                id="websiteMenu"
                title="Website Management"
                icon="bi bi-globe"
                :active="menu_active('website')">

                <x-admin.menu-item
                    route="admin.website-settings.index"
                    icon="bi bi-gear"
                    label="Website Settings"
                />

                <x-admin.menu-item
                    route="admin.home-slider.index"
                    icon="bi bi-images"
                    label="Home Slider"
                />

                <x-admin.menu-item
                    route="admin.news.index"
                    icon="bi bi-newspaper"
                    label="News & Notices"
                />

                <x-admin.menu-item
                    route="admin.events.index"
                    icon="bi bi-calendar-event"
                    label="Events"
                />

                <x-admin.menu-item
                    route="admin.gallery.index"
                    icon="bi bi-image"
                    label="Gallery"
                />

                <x-admin.menu-item
                    route="admin.contact-messages.index"
                    icon="bi bi-envelope"
                    label="Contact Messages"
                />

            </x-admin.menu-group>


            {{-- Academic --}}
            <x-admin.menu-group
                id="academicMenu"
                title="Academic Management"
                icon="bi bi-mortarboard"
                :active="menu_active('academic')">

                <x-admin.menu-item
                    route="admin.academic.index"
                    icon="bi bi-calendar3"
                    label="Academic Session"
                />

                <x-admin.menu-item
                    route="admin.classes.index"
                    active="admin.classes.*"
                    icon="bi bi-building"
                    label="Classes"
                />

                <x-admin.menu-item
                    route="admin.sections.index"
                    active="admin.sections.*"
                    icon="bi bi-diagram-3"
                    label="Sections"
                />

                <x-admin.menu-item
                    route="admin.class-sections.index"
                    active="admin.class-sections.*"
                    icon="bi bi-diagram-2"
                    label="Class Sections"
                />

                <x-admin.menu-item
                    route="admin.subjects.index"
                    active="admin.subjects.*"
                    icon="bi bi-book"
                    label="Subjects"
                />

                <x-admin.menu-item
                    route="admin.clsubject.index"
                    active="admin.clsubject.*"
                    icon="bi bi-journal-bookmark"
                    label="Class Subjects"
                />

                <x-admin.menu-item
                    route="admin.teachers.index"
                    active="admin.teachers.*"
                    icon="bi bi-person-workspace"
                    label="Teachers"
                />

                <x-admin.menu-item
                    route="admin.teacher-subject.index"
                    active="admin.teacher-subject.*"
                    icon="bi bi-person-video2"
                    label="Teacher Subjects"
                />

                <x-admin.menu-item
                    route="admin.teacher_attendance.index"
                    active="admin.teacher_attendance.*"
                    icon="bi bi-calendar-check"
                    label="Teacher Attendance"
                />

                <x-admin.menu-item
                    route="admin.periods.index"
                    active="admin.periods.*"
                    icon="bi bi-calendar-week"
                    label="Academic Periods"
                />
                <x-admin.menu-item
                    route="admin.class-timetables.index"
                    active="admin.class-timetables.*"
                    icon="bi bi-clock-history"
                    label="Class Timetable"
                />

            </x-admin.menu-group>


            {{-- Students --}}
            <x-admin.menu-group
                id="studentMenu"
                title="Students Management"
                icon="bi bi-people"
                :active="menu_active('students')">

                <x-admin.menu-item
                    route="admin.students.index"
                    active="admin.students.*"
                    icon="bi bi-list-ul"
                    label="Student List"
                />

                <x-admin.menu-item
                    route="admin.student-promotions.index"
                    active="admin.student-promotions.*"
                    icon="bi bi-mortarboard-fill"
                    label="Student Promotion"
                />

                {{-- Not implemented yet --}}
                <li>
                    <a href="#">
                        <i class="bi bi-person-plus me-2"></i>
                        <span>Admissions</span>
                    </a>
                </li>

                <x-admin.menu-item
                    route="admin.attendance.index"
                    active="admin.attendance.*"
                    icon="bi bi-calendar-check"
                    label="Attendance"
                />

                <x-admin.menu-item
                    route="admin.admission-enquiry.index"
                    active="admin.admission-enquiry.*"
                    icon="bi bi-person-lines-fill"
                    label="Admission Enquiry"
                />

            </x-admin.menu-group>


            {{-- Examinations --}}
            <x-admin.menu-group
                id="examMenu"
                title="Examinations"
                icon="bi bi-journal-check"
                :active="menu_active('examinations')">

                <x-admin.menu-item
                    route="admin.questions.index"
                    active="admin.questions.*"
                    icon="bi bi-patch-question"
                    label="Question Bank"
                />

                <x-admin.menu-item
                    route="admin.question-papers.index"
                    active="admin.question-papers.*"
                    icon="bi bi-file-earmark-text"
                    label="Question Papers"
                />

                <x-admin.menu-item
                    route="admin.exams.index"
                    active="admin.exams.*"
                    icon="bi bi-journal-text"
                    label="Exams"
                />

                <x-admin.menu-item
                    route="admin.results.index"
                    active="admin.results.*"
                    icon="bi bi-award"
                    label="Results"
                />

            </x-admin.menu-group>


            {{-- Website Management --}}
            <x-admin.menu-group
                id="websiteMenu"
                title="Website Management"
                icon="bi bi-globe"
                :active="menu_active('website')">

                <x-admin.menu-item
                    route="admin.home-slider.index"
                    active="admin.home-slider.*"
                    icon="bi bi-images"
                    label="Home Sliders"
                />

                <x-admin.menu-item
                    route="admin.news.index"
                    active="admin.news.*"
                    icon="bi bi-newspaper"
                    label="News & Notices"
                />

                <x-admin.menu-item
                    route="admin.events.index"
                    active="admin.events.*"
                    icon="bi bi-calendar-event"
                    label="Events"
                />

                <x-admin.menu-item
                    route="admin.gallery.index"
                    active="admin.gallery.*"
                    icon="bi bi-camera"
                    label="Photo Gallery"
                />

                <x-admin.menu-item
                    route="admin.contact-messages.index"
                    active="admin.contact-messages.*"
                    icon="bi bi-envelope"
                    label="Contact Messages"
                />

                <x-admin.menu-item
                    route="admin.website-settings.index"
                    active="admin.website-settings.*"
                    icon="bi bi-sliders"
                    label="Website Settings"
                />

            </x-admin.menu-group>


            {{-- Administration --}}
            <li class="menu-title">
                ADMINISTRATION
            </li>


            {{-- User Management --}}
            <x-admin.menu-group
                id="userMenu"
                title="User Management"
                icon="bi bi-shield-lock"
                :active="menu_active('users')">

                <x-admin.menu-item
                    route="admin.roles.index"
                    active="admin.roles.*"
                    icon="bi bi-person-badge"
                    label="Roles"
                />

                <li>
                    <a href="#">
                        <i class="bi bi-shield-lock me-2"></i>
                        <span>Permissions</span>
                    </a>
                </li>

                <x-admin.menu-item
                    route="admin.staffs.index"
                    active="admin.staffs.*"
                    icon="bi bi-person-gear"
                    label="Admin Users"
                />

            </x-admin.menu-group>


            {{-- Reports --}}
            <li>

                <a href="#" title="Reports">

                    <i class="bi bi-bar-chart"></i>

                    <span>Reports</span>

                </a>

            </li>


            {{-- Settings --}}
            <x-admin.menu-group
                id="settingsMenu"
                title="Settings"
                icon="bi bi-gear"
                :active="menu_active('settings')">

                <x-admin.menu-item
                    route="admin.logs.index"
                    active="admin.logs.*"
                    icon="bi bi-file-text"
                    label="Logs"
                />

                <li>
                    <a href="#">
                        <i class="bi bi-upload me-2"></i>
                        <span>Import</span>
                    </a>
                </li>

            </x-admin.menu-group>

        </ul>

    </nav>

</aside>
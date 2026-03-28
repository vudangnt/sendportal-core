{{-- Example Email Templates Gallery Content (used inside tabs) --}}
<div class="card-body">
    {{-- Search and toggle --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            <i class="fas fa-info-circle mr-1"></i>
            {{ __('Choose a pre-designed template to get started') }}
        </p>
        <div>
            <input type="text" id="example-search" class="form-control form-control-sm d-inline-block" placeholder="{{ __('Search...') }}" style="width: 200px;">
        </div>
    </div>

    {{-- Category filter --}}
    <div class="mb-3" style="line-height: 2.2;">
        <button class="btn btn-sm btn-primary example-filter active" data-category="all">{{ __('All') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="welcome">{{ __('Welcome') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="newsletter">{{ __('Newsletter') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="promotion">{{ __('Promotion') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="event">{{ __('Event') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="notification">{{ __('Notification') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="recruitment">{{ __('Recruitment') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="healthcare">{{ __('Healthcare') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="education">{{ __('Education') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="accounting">{{ __('Accounting') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="realestate">{{ __('Real Estate') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="restaurant">{{ __('Restaurant') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="fitness">{{ __('Fitness') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="technology">{{ __('Technology') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="beauty">{{ __('Beauty') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="legal">{{ __('Legal') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="travel">{{ __('Travel') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="nonprofit">{{ __('Nonprofit') }}</button>
        <button class="btn btn-sm btn-outline-secondary example-filter" data-category="ecommerce">{{ __('E-Commerce') }}</button>
    </div>

    <div class="row" id="example-grid">
        {{-- 1. Blank Template --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="all">
            <div class="card h-100 example-card" data-template="blank" style="cursor:pointer;">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 160px; background: #f8f9fa; border: 2px dashed #dee2e6;">
                    <i class="fas fa-plus fa-2x text-muted mb-2"></i>
                    <small class="text-muted font-weight-bold">{{ __('Blank') }}</small>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Start from Scratch') }}</small>
                </div>
            </div>
        </div>

        {{-- 2. Welcome Email --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="welcome">
            <div class="card h-100 example-card" data-template="welcome" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-hand-sparkles fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">WELCOME</div>
                        <div style="font-size:9px; opacity:0.8;">Thank you for joining</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Welcome Email') }}</small>
                </div>
            </div>
        </div>

        {{-- 3. Newsletter --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="newsletter">
            <div class="card h-100 example-card" data-template="newsletter" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-newspaper fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">NEWSLETTER</div>
                        <div style="font-size:9px; opacity:0.8;">Weekly digest</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Newsletter') }}</small>
                </div>
            </div>
        </div>

        {{-- 4. Product Launch --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
            <div class="card h-100 example-card" data-template="product_launch" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-rocket fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">NEW PRODUCT</div>
                        <div style="font-size:9px; opacity:0.8;">Launch announcement</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Product Launch') }}</small>
                </div>
            </div>
        </div>

        {{-- 5. Sale / Promotion --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
            <div class="card h-100 example-card" data-template="sale_promotion" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-percentage fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">FLASH SALE</div>
                        <div style="font-size:9px; opacity:0.8;">Up to 50% OFF</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Sale Promotion') }}</small>
                </div>
            </div>
        </div>

        {{-- 6. Event Invitation --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="event">
            <div class="card h-100 example-card" data-template="event_invitation" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-calendar-check fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">EVENT</div>
                        <div style="font-size:9px; opacity:0.8;">You're invited!</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Event Invitation') }}</small>
                </div>
            </div>
        </div>

        {{-- 7. Thank You --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
            <div class="card h-100 example-card" data-template="thank_you" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-heart fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">THANK YOU</div>
                        <div style="font-size:9px; opacity:0.8;">We appreciate you</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Thank You') }}</small>
                </div>
            </div>
        </div>

        {{-- 8. Re-engagement --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
            <div class="card h-100 example-card" data-template="re_engagement" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-undo fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">MISS YOU</div>
                        <div style="font-size:9px; opacity:0.8;">Come back!</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Re-engagement') }}</small>
                </div>
            </div>
        </div>

        {{-- 9. Job Recruitment --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="recruitment">
            <div class="card h-100 example-card" data-template="recruitment" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #0c3483 0%, #a2b6df 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-briefcase fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">WE'RE HIRING</div>
                        <div style="font-size:9px; opacity:0.8;">Join our team</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Recruitment') }}</small>
                </div>
            </div>
        </div>

        {{-- 10. Holiday Greeting --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="event">
            <div class="card h-100 example-card" data-template="holiday_greeting" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-gift fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">HAPPY HOLIDAYS</div>
                        <div style="font-size:9px; opacity:0.8;">Season's greetings</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Holiday Greeting') }}</small>
                </div>
            </div>
        </div>

        {{-- 11. Simple Text --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="newsletter">
            <div class="card h-100 example-card" data-template="simple_text" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-align-left fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">SIMPLE TEXT</div>
                        <div style="font-size:9px; opacity:0.8;">Clean & minimal</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Simple Text') }}</small>
                </div>
            </div>
        </div>

        {{-- 12. Announcement --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
            <div class="card h-100 example-card" data-template="announcement" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #536976 0%, #292E49 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-bullhorn fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">ANNOUNCEMENT</div>
                        <div style="font-size:9px; opacity:0.8;">Important update</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Announcement') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Healthcare --}}
        {{-- ============================================ --}}

        {{-- 13. Healthcare - Appointment Reminder --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="healthcare">
            <div class="card h-100 example-card" data-template="healthcare_appointment" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-calendar-alt fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">APPOINTMENT</div>
                        <div style="font-size:9px; opacity:0.8;">Reminder</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Appointment Reminder') }}</small>
                </div>
            </div>
        </div>

        {{-- 14. Healthcare - Health Tips --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="healthcare">
            <div class="card h-100 example-card" data-template="healthcare_tips" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-heartbeat fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">HEALTH TIPS</div>
                        <div style="font-size:9px; opacity:0.8;">Stay healthy</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Health Tips') }}</small>
                </div>
            </div>
        </div>

        {{-- 15. Healthcare - Patient Welcome --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="healthcare">
            <div class="card h-100 example-card" data-template="healthcare_welcome" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-hospital-user fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">PATIENT</div>
                        <div style="font-size:9px; opacity:0.8;">Welcome to our clinic</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Patient Welcome') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Education --}}
        {{-- ============================================ --}}

        {{-- 16. Education - Course Enrollment --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="education">
            <div class="card h-100 example-card" data-template="education_enrollment" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #4568DC 0%, #B06AB3 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-graduation-cap fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">ENROLLMENT</div>
                        <div style="font-size:9px; opacity:0.8;">Course registration</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Course Enrollment') }}</small>
                </div>
            </div>
        </div>

        {{-- 17. Education - Online Course --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="education">
            <div class="card h-100 example-card" data-template="education_course" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #FF512F 0%, #F09819 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-laptop-code fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">ONLINE COURSE</div>
                        <div style="font-size:9px; opacity:0.8;">Learn new skills</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Online Course') }}</small>
                </div>
            </div>
        </div>

        {{-- 18. Education - Webinar Invite --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="education">
            <div class="card h-100 example-card" data-template="education_webinar" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #1A2980 0%, #26D0CE 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">WEBINAR</div>
                        <div style="font-size:9px; opacity:0.8;">Join live session</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Webinar Invite') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Accounting / Finance --}}
        {{-- ============================================ --}}

        {{-- 19. Accounting - Invoice --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="accounting">
            <div class="card h-100 example-card" data-template="accounting_invoice" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2C3E50 0%, #3498DB 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">INVOICE</div>
                        <div style="font-size:9px; opacity:0.8;">Payment reminder</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Invoice Reminder') }}</small>
                </div>
            </div>
        </div>

        {{-- 20. Accounting - Financial Report --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="accounting">
            <div class="card h-100 example-card" data-template="accounting_report" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #134E5E 0%, #71B280 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-chart-line fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">REPORT</div>
                        <div style="font-size:9px; opacity:0.8;">Financial summary</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Financial Report') }}</small>
                </div>
            </div>
        </div>

        {{-- 21. Accounting - Tax Season --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="accounting">
            <div class="card h-100 example-card" data-template="accounting_tax" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #1D4350 0%, #A43931 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-calculator fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">TAX SEASON</div>
                        <div style="font-size:9px; opacity:0.8;">Important deadlines</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Tax Season') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Real Estate --}}
        {{-- ============================================ --}}

        {{-- 22. Real Estate - Property Listing --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="realestate">
            <div class="card h-100 example-card" data-template="realestate_listing" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #C33764 0%, #1D2671 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-home fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">PROPERTY</div>
                        <div style="font-size:9px; opacity:0.8;">New listing</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Property Listing') }}</small>
                </div>
            </div>
        </div>

        {{-- 23. Real Estate - Open House --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="realestate">
            <div class="card h-100 example-card" data-template="realestate_openhouse" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #E44D26 0%, #F16529 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-door-open fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">OPEN HOUSE</div>
                        <div style="font-size:9px; opacity:0.8;">Visit this weekend</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Open House') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Restaurant / Food --}}
        {{-- ============================================ --}}

        {{-- 24. Restaurant - Menu Update --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="restaurant">
            <div class="card h-100 example-card" data-template="restaurant_menu" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #D4145A 0%, #FBB03B 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-utensils fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">NEW MENU</div>
                        <div style="font-size:9px; opacity:0.8;">Fresh dishes</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Menu Update') }}</small>
                </div>
            </div>
        </div>

        {{-- 25. Restaurant - Reservation --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="restaurant">
            <div class="card h-100 example-card" data-template="restaurant_reservation" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #6B0F1A 0%, #B91D73 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-concierge-bell fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">RESERVATION</div>
                        <div style="font-size:9px; opacity:0.8;">Book your table</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Reservation') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Fitness / Gym --}}
        {{-- ============================================ --}}

        {{-- 26. Fitness - Membership --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="fitness">
            <div class="card h-100 example-card" data-template="fitness_membership" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-dumbbell fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">MEMBERSHIP</div>
                        <div style="font-size:9px; opacity:0.8;">Join our gym</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Gym Membership') }}</small>
                </div>
            </div>
        </div>

        {{-- 27. Fitness - Workout Plan --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="fitness">
            <div class="card h-100 example-card" data-template="fitness_workout" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #0F2027 0%, #203A43 50%, #2C5364 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-running fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">WORKOUT</div>
                        <div style="font-size:9px; opacity:0.8;">Weekly plan</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Workout Plan') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Technology --}}
        {{-- ============================================ --}}

        {{-- 28. Technology - Product Update --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="technology">
            <div class="card h-100 example-card" data-template="tech_update" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-microchip fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">TECH UPDATE</div>
                        <div style="font-size:9px; opacity:0.8;">New features</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Tech Update') }}</small>
                </div>
            </div>
        </div>

        {{-- 29. Technology - SaaS Onboarding --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="technology">
            <div class="card h-100 example-card" data-template="tech_onboarding" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-cloud fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">ONBOARDING</div>
                        <div style="font-size:9px; opacity:0.8;">Get started</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('SaaS Onboarding') }}</small>
                </div>
            </div>
        </div>

        {{-- 30. Technology - Security Alert --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="technology">
            <div class="card h-100 example-card" data-template="tech_security" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #333333 0%, #dd1818 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-shield-alt fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">SECURITY</div>
                        <div style="font-size:9px; opacity:0.8;">Important alert</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Security Alert') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Beauty / Spa --}}
        {{-- ============================================ --}}

        {{-- 31. Beauty - Spa Promotion --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="beauty">
            <div class="card h-100 example-card" data-template="beauty_spa" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #e8cbc0 0%, #636fa4 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-spa fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">SPA & BEAUTY</div>
                        <div style="font-size:9px; opacity:0.8;">Treat yourself</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Spa Promotion') }}</small>
                </div>
            </div>
        </div>

        {{-- 32. Beauty - New Collection --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="beauty">
            <div class="card h-100 example-card" data-template="beauty_collection" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-magic fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">NEW COLLECTION</div>
                        <div style="font-size:9px; opacity:0.8;">Beauty essentials</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Beauty Collection') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Legal --}}
        {{-- ============================================ --}}

        {{-- 33. Legal - Newsletter --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="legal">
            <div class="card h-100 example-card" data-template="legal_newsletter" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #1c1c1c 0%, #434343 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-balance-scale fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">LEGAL UPDATE</div>
                        <div style="font-size:9px; opacity:0.8;">Law & compliance</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Legal Newsletter') }}</small>
                </div>
            </div>
        </div>

        {{-- 34. Legal - Client Update --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="legal">
            <div class="card h-100 example-card" data-template="legal_client" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #373B44 0%, #4286f4 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-gavel fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">CLIENT UPDATE</div>
                        <div style="font-size:9px; opacity:0.8;">Case status</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Client Update') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Travel --}}
        {{-- ============================================ --}}

        {{-- 35. Travel - Destination --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="travel">
            <div class="card h-100 example-card" data-template="travel_destination" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #1CB5E0 0%, #000851 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-plane fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">TRAVEL DEALS</div>
                        <div style="font-size:9px; opacity:0.8;">Explore the world</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Travel Deals') }}</small>
                </div>
            </div>
        </div>

        {{-- 36. Travel - Booking Confirmation --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="travel">
            <div class="card h-100 example-card" data-template="travel_booking" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-hotel fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">BOOKING</div>
                        <div style="font-size:9px; opacity:0.8;">Confirmation</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Booking Confirmation') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - Nonprofit --}}
        {{-- ============================================ --}}

        {{-- 37. Nonprofit - Donation --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="nonprofit">
            <div class="card h-100 example-card" data-template="nonprofit_donation" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-hand-holding-heart fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">DONATE</div>
                        <div style="font-size:9px; opacity:0.8;">Make a difference</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Donation Appeal') }}</small>
                </div>
            </div>
        </div>

        {{-- 38. Nonprofit - Volunteer --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="nonprofit">
            <div class="card h-100 example-card" data-template="nonprofit_volunteer" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-hands-helping fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">VOLUNTEER</div>
                        <div style="font-size:9px; opacity:0.8;">Join our cause</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Volunteer Call') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- NEW TEMPLATES - E-Commerce --}}
        {{-- ============================================ --}}

        {{-- 39. E-Commerce - Order Confirmation --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="ecommerce">
            <div class="card h-100 example-card" data-template="ecommerce_order" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #3a1c71 0%, #d76d77 50%, #ffaf7b 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-shopping-bag fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">ORDER</div>
                        <div style="font-size:9px; opacity:0.8;">Confirmation</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Order Confirmation') }}</small>
                </div>
            </div>
        </div>

        {{-- 40. E-Commerce - Abandoned Cart --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="ecommerce">
            <div class="card h-100 example-card" data-template="ecommerce_cart" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #e53935 0%, #e35d5b 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-shopping-cart fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">CART</div>
                        <div style="font-size:9px; opacity:0.8;">Don't forget!</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Abandoned Cart') }}</small>
                </div>
            </div>
        </div>

        {{-- 41. E-Commerce - Review Request --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="ecommerce">
            <div class="card h-100 example-card" data-template="ecommerce_review" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-star fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">REVIEW</div>
                        <div style="font-size:9px; opacity:0.8;">Share your thoughts</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Review Request') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- MORE RECRUITMENT TEMPLATES --}}
        {{-- ============================================ --}}

        {{-- 42. Recruitment - Interview Invite --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="recruitment">
            <div class="card h-100 example-card" data-template="recruitment_interview" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-user-tie fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">INTERVIEW</div>
                        <div style="font-size:9px; opacity:0.8;">Invitation</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Interview Invite') }}</small>
                </div>
            </div>
        </div>

        {{-- 43. Recruitment - Offer Letter --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="recruitment">
            <div class="card h-100 example-card" data-template="recruitment_offer" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #2980B9 0%, #6DD5FA 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-file-signature fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">OFFER</div>
                        <div style="font-size:9px; opacity:0.8;">Congratulations!</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Offer Letter') }}</small>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- MORE MARKETING TEMPLATES --}}
        {{-- ============================================ --}}

        {{-- 44. Marketing - Referral Program --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
            <div class="card h-100 example-card" data-template="marketing_referral" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #F7971E 0%, #FFD200 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-users fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">REFERRAL</div>
                        <div style="font-size:9px; opacity:0.8;">Invite & earn</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Referral Program') }}</small>
                </div>
            </div>
        </div>

        {{-- 45. Marketing - Survey --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
            <div class="card h-100 example-card" data-template="marketing_survey" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #6a3093 0%, #a044ff 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-poll fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">SURVEY</div>
                        <div style="font-size:9px; opacity:0.8;">Share feedback</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Customer Survey') }}</small>
                </div>
            </div>
        </div>

        {{-- 46. Marketing - Loyalty Reward --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="promotion">
            <div class="card h-100 example-card" data-template="marketing_loyalty" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #C6426E 0%, #642B73 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-crown fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">LOYALTY</div>
                        <div style="font-size:9px; opacity:0.8;">VIP rewards</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Loyalty Rewards') }}</small>
                </div>
            </div>
        </div>

        {{-- 47. Birthday Greeting --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="event">
            <div class="card h-100 example-card" data-template="birthday_greeting" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-birthday-cake fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">BIRTHDAY</div>
                        <div style="font-size:9px; opacity:0.8;">Happy birthday!</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Birthday Greeting') }}</small>
                </div>
            </div>
        </div>

        {{-- 48. Password Reset --}}
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 example-item" data-category="notification">
            <div class="card h-100 example-card" data-template="password_reset" style="cursor:pointer;">
                <div class="card-body p-0" style="min-height: 160px; background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-2">
                        <i class="fas fa-key fa-2x mb-1"></i>
                        <div style="font-size:11px; font-weight:bold;">PASSWORD</div>
                        <div style="font-size:9px; opacity:0.8;">Reset request</div>
                    </div>
                </div>
                <div class="card-footer bg-white py-1 text-center">
                    <small class="font-weight-bold">{{ __('Password Reset') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .example-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 2px solid transparent;
    }
    .example-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    .example-card.selected {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.3);
    }
    .example-filter.active {
        background-color: #667eea;
        border-color: #667eea;
    }
</style>

{{-- Include the exampleTemplates JS data from the original partial --}}
@include('sendportal::templates.partials.example-templates-data')

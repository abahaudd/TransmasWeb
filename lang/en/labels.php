<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared field labels
    |--------------------------------------------------------------------------
    |
    | Reused across many resources/forms. Prefer these over duplicating a
    | translation before adding a new key — see labels.<resource> below for
    | anything specific to one form.
    */

    'name' => 'Name',
    'email' => 'Email',
    'email_address' => 'Email address',
    'phone' => 'Phone',
    'mobile' => 'Mobile',
    'website' => 'Website',
    'status' => 'Status',
    'type' => 'Type',
    'code' => 'Code',
    'country' => 'Country',
    'address' => 'Address',
    'remarks' => 'Remarks',
    'notes' => 'Notes',
    'active' => 'Active',
    'primary' => 'Primary',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'username' => 'Username',
    'password' => 'Password',
    'verify_password' => 'Verify password',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'view' => 'View',
    'edit' => 'Edit',

    /*
    |--------------------------------------------------------------------------
    | Navigation: groups, resource nav labels, page titles
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'groups' => [
            'cms' => 'CMS',
            'customer_management' => 'Customer Management',
            'hr_management' => 'HR Management',
            'control_panel' => 'Control Panel',
            'administration' => 'Administration',
        ],
        'dashboard' => 'Dashboard',
        'pages' => 'Pages',
        'blocks' => 'Blocks',
        'companies' => 'Companies',
        'customers' => 'Customers',
        'employees' => 'Employees',
        'departments' => 'Departments',
        'designations' => 'Designations',
        'employment_types' => 'Employment Types',
        'employment_statuses' => 'Employment Statuses',
        'sequence_number_formats' => 'Sequence Number Formats',
        'countries' => 'Countries',
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'users' => 'Users',
        'managers' => 'Managers',
        'staff' => 'Staff',
        'sales' => 'Sales',
        'activity_logs' => 'Activity Logs',
        'site_settings' => 'Site Settings',
        'company_settings' => 'Company Settings',
        'formatting_settings' => 'Formatting Settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Company (business entity: Company/Branch/Warehouse/Factory/Office)
    |--------------------------------------------------------------------------
    */

    'company' => [
        'section_details' => 'Company Details',
        'section_registration_locale' => 'Registration & Locale',
        'section_working_hours' => 'Working Hours',
        'section_branding_notes' => 'Branding & Notes',
        'company_type' => 'Type',
        'company_code' => 'Company Code',
        'legal_name' => 'Legal Name',
        'trade_name' => 'Trade Name',
        'display_name' => 'Display Name',
        'display_name_helper' => 'Shown throughout the app when set; falls back to trade or legal name.',
        'parent_company' => 'Parent Company',
        'tax_country' => 'Tax Country',
        'timezone' => 'Timezone',
        'start_work_hour' => 'Start',
        'end_work_hour' => 'End',
        'weekends' => 'Weekends',
        'incorporation_date' => 'Incorporation Date',
        'financial_year_start' => 'Financial Year Start',
        'logo' => 'Logo',
    ],

    'phone_record' => [
        'section_title' => 'Phones',
        'type' => 'Type',
        'country_code' => 'Country Code',
        'phone_number' => 'Phone Number',
        'extension' => 'Extension',
        'contact_name' => 'Contact Name',
    ],

    'government_registration' => [
        'section_title' => 'Government Registrations',
        'registration_type' => 'Registration Type',
        'registration_type_helper' => 'Pick a common type or type your own — no schema change needed.',
        'registration_number' => 'Number',
        'issuing_authority' => 'Issuing Authority',
        'issue_date' => 'Issue Date',
        'expiry_date' => 'Expiry Date',
        'document' => 'Document',
        'country' => 'Country',
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    'customer' => [
        'section_details' => 'Customer Details',
        'section_location_details' => 'Location Details',
        'parent_customer' => 'Parent customer',
        'sales_staff' => 'Sales Staff',
        'assign_sales_staff' => 'Assign Sales Staff',
        'assigned_customers' => 'Assigned Customers',
        'country_calling_code' => 'Country calling code',
        'select_customers_for_assignment' => 'Select customers for assigning sales staff',
        'uncheck_to_remove' => 'Uncheck customers to remove them from this sales staff member.',
        'branch_badge' => 'Branch',
        'create_account' => 'Create Account',
        'sales_staff_fallback' => 'Sales Staff #:id',
        'save_assignment' => 'Save Assignment',
    ],

    'account_user' => [
        'section_title' => 'Account Users',
        'create_date' => 'Create Date',
        'account_active' => 'Account Active',
        'has_account_notice' => 'This customer has an account',
        'user_id' => 'User ID',
        'leave_blank_password' => 'Leave blank to keep existing password.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Employee / HR
    |--------------------------------------------------------------------------
    */

    'employee' => [
        'section_personal_details' => 'Personal Details',
        'section_employment_details' => 'Employment Details',
        'section_addresses' => 'Addresses',
        'addresses_description' => 'An employee may have more than one address on file, e.g. current and permanent.',
        'employee_code' => 'Employee Code',
        'employee_code_helper' => 'Auto-generated on save.',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'gender' => 'Gender',
        'birth_date' => 'Birth Date',
        'nationality' => 'Nationality',
        'national_id' => 'National ID',
        'company' => 'Company',
        'department' => 'Department',
        'designation' => 'Designation',
        'employment_type' => 'Employment Type',
        'employment_status' => 'Employment Status',
        'reports_to' => 'Reports To',
        'is_manager' => 'Is Manager',
        'linked_user_account' => 'Linked User Account',
        'joining_date' => 'Joining Date',
        'confirmation_date' => 'Confirmation Date',
        'end_date' => 'End Date',
        'termination_reason' => 'Termination Reason',
        'city_location' => 'City / Location',
        'state_territory' => 'State / Territory',
        'postal_code' => 'Postal Code',
        'gender_male' => 'Male',
        'gender_female' => 'Female',
        'gender_other' => 'Other',
        'add_address' => 'Add address',
    ],

    'department' => [
        'company' => 'Company',
        'company_helper' => "Leave empty to make this department available to every company.",
        'all_companies' => 'All companies',
    ],

    'designation' => [
        'title' => 'Title',
        'department' => 'Department',
        'department_helper' => 'Leave empty to make this designation available across departments.',
        'all_departments' => 'All departments',
    ],

    'employment_type' => [
        //
    ],

    'permission' => [
        'guard_name' => 'Guard Name',
    ],

    'user' => [
        'email_verified_at' => 'Email Verified At',
    ],

    'country_fields' => [
        'country_code' => 'Country Code',
        'country_code_alpha3' => 'Alpha-3 Code',
        'location_title' => 'Location Title',
        'territory_title' => 'Territory Title',
        'postal_code_title' => 'Postal Code Title',
    ],

    'employment_status' => [
        'ends_employment' => 'Ends employment',
        'ends_employment_helper' => 'e.g. Resigned, Terminated, Retired, Deceased — excluded from active headcount.',
    ],

    'sequence_number_format' => [
        'category' => 'Category',
        'category_helper' => 'e.g. invoice, receipt, employee — the key code passed to SequenceNumberService::next().',
        'prefix' => 'Prefix',
        'separator' => 'Separator',
        'current_number' => 'Current Number',
        'current_number_helper' => 'The last number issued. The next call to next() returns this value + 1.',
        'zero_pad_length' => 'Zero-pad Length',
        'zero_pad_length_helper' => 'Total digit width of the numeric part, zero-padded. Leave blank for no padding.',
        'next_number_preview' => 'Next Number Preview',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS
    |--------------------------------------------------------------------------
    */

    'cms_page' => [
        'model_label' => 'Page',
        'section_page' => 'Page',
        'section_seo' => 'SEO',
        'title' => 'Title',
        'slug' => 'Slug',
        'template' => 'Template',
        'slug_helper' => 'The page URL: /{slug}. Use "home" for the front page.',
        'template' => 'Template',
        'published' => 'Published',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'blocks' => 'Blocks',
        'view_page' => 'View page',
    ],

    'block' => [
        'page' => 'Page',
        'type' => 'Type',
        'name' => 'Name',
        'name_helper' => 'Internal label shown only in this list.',
        'position' => 'Position',
        'active' => 'Active',
        'content_json' => 'Content (JSON)',
        'content_json_helper' => 'The structured content this block renders. Keys depend on the block type.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity log
    |--------------------------------------------------------------------------
    */

    'activity_log' => [
        'model_label' => 'Activity Log',
        'section_activity' => 'Activity',
        'section_changes' => 'Changes',
        'when' => 'When',
        'log' => 'Log',
        'event' => 'Event',
        'description' => 'Description',
        'subject' => 'Subject',
        'by' => 'By',
        'system' => 'System',
        'recorded_properties' => 'Recorded properties',
        'events' => [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings pages
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'general' => [
            'section_site' => 'Site',
            'section_site_description' => 'Identity of this application, available to all modules.',
            'site_name' => 'Site name',
            'site_description' => 'Site description',
            'support_email' => 'Support email',
            'section_company' => 'Company',
            'section_company_description' => 'Shown in the public site header and footer.',
            'company_name' => 'Company name',
            'logo' => 'Logo',
            'logo_helper' => 'Displayed in the site navigation next to the company name.',
            'section_contact' => 'Company Contact details',
            'section_contact_description' => 'Shown in the public site footer.',
            'section_localization' => 'Localization',
            'default_locale' => 'Default locale',
            'timezone' => 'Timezone',
        ],
        'company' => [
            'section_identity' => 'Identity',
            'section_identity_description' => 'Shown in the public site header and footer.',
            'company_name' => 'Company name',
            'logo' => 'Logo',
            'logo_helper' => 'Displayed in the site navigation next to the company name.',
            'section_contact' => 'Contact details',
            'section_contact_description' => 'Shown in the public site footer.',
        ],
        'formatting' => [
            'section_currency' => 'Currency',
            'section_currency_description' => 'Controls how monetary amounts are displayed app-wide via the Format helper.',
            'currency_symbol' => 'Currency symbol',
            'currency_symbol_helper' => 'The official new AED symbol (U+20C3) can be pasted here once your system font supports it; plain "AED" is used until then.',
            'symbol_position' => 'Symbol position',
            'symbol_position_before' => 'Before amount (AED 1,234.50)',
            'symbol_position_after' => 'After amount (1,234.50 AED)',
            'thousands_separator' => 'Thousands separator',
            'decimal_separator' => 'Decimal separator',
            'decimal_places' => 'Decimal places',
            'section_date' => 'Date',
            'section_date_description' => 'Controls how dates are displayed app-wide via the Format helper.',
            'date_format' => 'Date format',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'username' => 'Username',
        'email_or_username' => 'Email or username',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard / widgets
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title' => 'Dashboard',
    ],

    'widgets' => [
        'recent_activity' => 'Recent Activity',
        'when' => 'When',
        'log' => 'Log',
        'by' => 'By',
        'properties' => 'Properties',
        'properties_description' => ':count added this month',
        'tenants' => 'Tenants',
        'tenants_description' => ':percent% occupied',
        'outstanding_rent' => 'Outstanding Rent',
        'outstanding_rent_description' => ':count overdue',
        'maintenance' => 'Maintenance',
        'maintenance_description' => ':count urgent',
        'users' => 'Users',
        'roles' => 'Roles',
        'activity_24h' => 'Activity (24h)',
    ],

];

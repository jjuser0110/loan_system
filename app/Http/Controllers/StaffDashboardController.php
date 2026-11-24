<?php

namespace App\Http\Controllers;

/**
 * Same as DashboardController, but renders the staff dashboard view.
 * (Previously a full 667-line copy of DashboardController.)
 */
class StaffDashboardController extends DashboardController
{
    protected $dashboardView = 'staffdashboard';
}

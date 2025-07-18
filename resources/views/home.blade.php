@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Default Layout</h2>

    <div class="right-wrapper text-end">
        <ol class="breadcrumbs">
            <li>
                <a href="{{route('home')}}">
                    <i class="bx bx-home-alt"></i>
                </a>
            </li>

            <li><span>Layouts</span></li>

        </ol>

        <span class="sidebar-right-toggle"><i class="fas fa-chevron-left"></i></span>
    </div>
</header>

@include('layouts.flash-message')
<!-- start: page -->
<div class="row">
    <div class="col-lg-6 mb-3">
        <section class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="chart-data-selector" id="salesSelectorWrapper">
                            <h2>
                                Sales:
                                <strong>
                                    <select class="form-control" id="salesSelector">
                                        <option value="Porto Admin" selected>Porto Admin</option>
                                        <option value="Porto Drupal" >Porto Drupal</option>
                                        <option value="Porto Wordpress" >Porto Wordpress</option>
                                    </select>
                                </strong>
                            </h2>

                            <div id="salesSelectorItems" class="chart-data-selector-items mt-3">
                                <!-- Flot: Sales Porto Admin -->
                                <div class="chart chart-sm" data-sales-rel="Porto Admin" id="flotDashSales1" class="chart-active" style="height: 203px;"></div>
                                <script>

                                    var flotDashSales1Data = [{
                                        data: [
                                            ["Jan", 140],
                                            ["Feb", 240],
                                            ["Mar", 190],
                                            ["Apr", 140],
                                            ["May", 180],
                                            ["Jun", 320],
                                            ["Jul", 270],
                                            ["Aug", 180]
                                        ],
                                        color: "#0088cc"
                                    }];

                                    // See: js/examples/examples.dashboard.js for more settings.

                                </script>

                                <!-- Flot: Sales Porto Drupal -->
                                <div class="chart chart-sm" data-sales-rel="Porto Drupal" id="flotDashSales2" class="chart-hidden"></div>
                                <script>

                                    var flotDashSales2Data = [{
                                        data: [
                                            ["Jan", 240],
                                            ["Feb", 240],
                                            ["Mar", 290],
                                            ["Apr", 540],
                                            ["May", 480],
                                            ["Jun", 220],
                                            ["Jul", 170],
                                            ["Aug", 190]
                                        ],
                                        color: "#2baab1"
                                    }];

                                    // See: js/examples/examples.dashboard.js for more settings.

                                </script>

                                <!-- Flot: Sales Porto Wordpress -->
                                <div class="chart chart-sm" data-sales-rel="Porto Wordpress" id="flotDashSales3" class="chart-hidden"></div>
                                <script>

                                    var flotDashSales3Data = [{
                                        data: [
                                            ["Jan", 840],
                                            ["Feb", 740],
                                            ["Mar", 690],
                                            ["Apr", 940],
                                            ["May", 1180],
                                            ["Jun", 820],
                                            ["Jul", 570],
                                            ["Aug", 780]
                                        ],
                                        color: "#734ba9"
                                    }];

                                    // See: js/examples/examples.dashboard.js for more settings.

                                </script>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-4 text-center">
                        <h2 class="card-title mt-3">Sales Goal</h2>
                        <div class="liquid-meter-wrapper liquid-meter-sm mt-3">
                            <div class="liquid-meter">
                                <meter min="0" max="100" value="35" id="meterSales"></meter>
                            </div>
                            <div class="liquid-meter-selector mt-4 pt-1" id="meterSalesSel">
                                <a href="#" data-val="35" class="active">Monthly Goal</a>
                                <a href="#" data-val="28">Annual Goal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <div class="row mb-3">
            <div class="col-xl-6">
                <section class="card card-featured-left card-featured-primary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-primary">
                                    <i class="fas fa-life-ring"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Support Questions</h4>
                                    <div class="info">
                                        <strong class="amount">1281</strong>
                                        <span class="text-primary">(14 unread)</span>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <a class="text-muted text-uppercase" href="#">(view all)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xl-6">
                <section class="card card-featured-left card-featured-secondary">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-secondary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Total Profit</h4>
                                    <div class="info">
                                        <strong class="amount">$ 14,890.30</strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <a class="text-muted text-uppercase" href="#">(withdraw)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <section class="card card-featured-left card-featured-tertiary mb-3">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-tertiary">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Today's Orders</h4>
                                    <div class="info">
                                        <strong class="amount">38</strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <a class="text-muted text-uppercase" href="#">(statement)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xl-6">
                <section class="card card-featured-left card-featured-quaternary">
                    <div class="card-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-quaternary">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Today's Visitors</h4>
                                    <div class="info">
                                        <strong class="amount">3765</strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <a class="text-muted text-uppercase" href="#">(report)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="row pt-4">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <section class="card">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">Best Seller</h2>
                <p class="card-subtitle">Customize the graphs as much as you want, there are so many options and features to display information using Porto Admin Template.</p>
            </header>
            <div class="card-body">

                <!-- Flot: Basic -->
                <div class="chart chart-md" id="flotDashBasic"></div>
                <script>

                    var flotDashBasicData = [{
                        data: [
                            [0, 170],
                            [1, 169],
                            [2, 173],
                            [3, 188],
                            [4, 147],
                            [5, 113],
                            [6, 128],
                            [7, 169],
                            [8, 173],
                            [9, 128],
                            [10, 128]
                        ],
                        label: "Series 1",
                        color: "#0088cc"
                    }, {
                        data: [
                            [0, 115],
                            [1, 124],
                            [2, 114],
                            [3, 121],
                            [4, 115],
                            [5, 83],
                            [6, 102],
                            [7, 148],
                            [8, 147],
                            [9, 103],
                            [10, 113]
                        ],
                        label: "Series 2",
                        color: "#2baab1"
                    }, {
                        data: [
                            [0, 70],
                            [1, 69],
                            [2, 73],
                            [3, 88],
                            [4, 47],
                            [5, 13],
                            [6, 28],
                            [7, 69],
                            [8, 73],
                            [9, 28],
                            [10, 28]
                        ],
                        label: "Series 3",
                        color: "#734ba9"
                    }];

                    // See: js/examples/examples.dashboard.js for more settings.

                </script>

            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="card">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>
                <h2 class="card-title">Server Usage</h2>
                <p class="card-subtitle">It's easy to create custom graphs on Porto Admin Template, there are several graph types that you can use.</p>
            </header>
            <div class="card-body">

                <!-- Flot: Curves -->
                <div class="chart chart-md" id="flotDashRealTime"></div>

            </div>
        </section>
    </div>
</div>

<div class="row pt-4 mt-2">
    <div class="col-lg-6 col-xl-3">
        <section class="card card-transparent">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">My Profile</h2>
            </header>
            <div class="card-body">
                <section class="card card-group">
                    <header class="card-header bg-primary w-100">

                        <div class="widget-profile-info">
                            <div class="profile-picture">
                                <img src="{{asset('porto-assets/img/!logged-user.jpg')}}">
                            </div>
                            <div class="profile-info">
                                <h4 class="name font-weight-semibold">John Doe</h4>
                                <h5 class="role">Administrator</h5>
                                <div class="profile-footer">
                                    <a href="#">(edit profile)</a>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div id="accordion" class="w-100">
                        <div class="card card-accordion card-accordion-first">
                            <div class="card-header border-bottom-0">
                                <h4 class="card-title">
                                    <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion" data-bs-target="#collapse1One">
                                        <i class="fas fa-check me-1"></i> Tasks
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse1One" class="accordion-body collapse show">
                                <div class="card-body">
                                    <ul class="widget-todo-list">
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" checked="" id="todoListItem1" class="todo-check">
                                                <label class="todo-label" for="todoListItem1"><span>Curabitur ac sem at nibh egestas urabitur ac sem at nibh egestas.</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem2" class="todo-check">
                                                <label class="todo-label" for="todoListItem2"><span>Lorem ipsum dolor sit amet</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem3" class="todo-check">
                                                <label class="todo-label" for="todoListItem3"><span>Curabitur ac sem at nibh egestas</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem4" class="todo-check">
                                                <label class="todo-label" for="todoListItem4"><span>Lorem ipsum dolor sit amet</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem5" class="todo-check">
                                                <label class="todo-label" for="todoListItem5"><span>Curabitur ac sem at nibh egestas.</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem6" class="todo-check">
                                                <label class="todo-label" for="todoListItem6"><span>Lorem ipsum dolor sit amet</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox-custom checkbox-default">
                                                <input type="checkbox" id="todoListItem7" class="todo-check">
                                                <label class="todo-label" for="todoListItem7"><span>Curabitur ac sem at nibh egestas.</span></label>
                                            </div>
                                            <div class="todo-actions">
                                                <a class="todo-remove" href="#">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                    <hr class="solid mt-3 mb-3">
                                    <form method="get" class="form-horizontal form-bordered">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control">
                                                    <button type="button" class="btn btn-primary" tabindex="-1">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card card-accordion">
                            <div class="card-header border-bottom-0">
                                <h4 class="card-title">
                                    <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion" data-bs-target="#collapse1Two">
                                            <i class="fas fa-comment me-1"></i> Messages
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse1Two" class="accordion-body collapse">
                                <div class="card-body">
                                    <ul class="simple-user-list mb-3">
                                        <li>
                                            <figure class="image rounded">
                                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joseph Doe Junior" class="rounded-circle">
                                            </figure>
                                            <span class="title">Joseph Doe Junior</span>
                                            <span class="message">Lorem ipsum dolor sit.</span>
                                        </li>
                                        <li>
                                            <figure class="image rounded">
                                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joseph Junior" class="rounded-circle">
                                            </figure>
                                            <span class="title">Joseph Junior</span>
                                            <span class="message">Lorem ipsum dolor sit.</span>
                                        </li>
                                        <li>
                                            <figure class="image rounded">
                                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joe Junior" class="rounded-circle">
                                            </figure>
                                            <span class="title">Joe Junior</span>
                                            <span class="message">Lorem ipsum dolor sit.</span>
                                        </li>
                                        <li>
                                            <figure class="image rounded">
                                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joseph Doe Junior" class="rounded-circle">
                                            </figure>
                                            <span class="title">Joseph Doe Junior</span>
                                            <span class="message">Lorem ipsum dolor sit.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </section>
    </div>
    <div class="col-lg-6 col-xl-3">
        <section class="card card-transparent">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">My Stats</h2>
            </header>
            <div class="card-body">
                <section class="card">
                    <div class="card-body">
                        <div class="small-chart float-end" id="sparklineBarDash"></div>
                        <script type="text/javascript">
                            var sparklineBarDashData = [5, 6, 7, 2, 0, 4 , 2, 4, 2, 0, 4 , 2, 4, 2, 0, 4];
                        </script>
                        <div class="h4 font-weight-bold mb-0">488</div>
                        <p class="text-3 text-muted mb-0">Average Profile Visits</p>
                    </div>
                </section>
                <section class="card">
                    <div class="card-body">
                        <div class="circular-bar circular-bar-xs m-0 mt-1 me-4 mb-0 float-end">
                            <div class="circular-bar-chart" data-percent="45" data-plugin-options='{ "barColor": "#2baab1", "delay": 300, "size": 50, "lineWidth": 4 }'>
                                <strong>Average</strong>
                                <label><span class="percent">45</span>%</label>
                            </div>
                        </div>
                        <div class="h4 font-weight-bold mb-0">12</div>
                        <p class="text-3 text-muted mb-0">Working Projects</p>
                    </div>
                </section>
                <section class="card">
                    <div class="card-body">
                        <div class="small-chart float-end" id="sparklineLineDash"></div>
                        <script type="text/javascript">
                            var sparklineLineDashData = [15, 16, 17, 19, 10, 15, 13, 12, 12, 14, 16, 17];
                        </script>
                        <div class="h4 font-weight-bold mb-0">89</div>
                        <p class="text-3 text-muted mb-0">Pending Tasks</p>
                    </div>
                </section>
            </div>
        </section>
        <section class="card mb-3">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">
                    <span class="badge badge-primary font-weight-normal va-middle p-2 me-e">298</span>
                    <span class="va-middle">Friends</span>
                </h2>
            </header>
            <div class="card-body">
                <div class="content">
                    <ul class="simple-user-list">
                        <li>
                            <figure class="image rounded">
                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joseph Doe Junior" class="rounded-circle">
                            </figure>
                            <span class="title">Joseph Doe Junior</span>
                            <span class="message truncate">Lorem ipsum dolor sit.</span>
                        </li>
                        <li>
                            <figure class="image rounded">
                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joseph Junior" class="rounded-circle">
                            </figure>
                            <span class="title">Joseph Junior</span>
                            <span class="message truncate">Lorem ipsum dolor sit.</span>
                        </li>
                        <li>
                            <figure class="image rounded">
                                <img src="{{asset('porto-assets/img/!sample-user.jpg')}}" alt="Joe Junior" class="rounded-circle">
                            </figure>
                            <span class="title">Joe Junior</span>
                            <span class="message truncate">Lorem ipsum dolor sit.</span>
                        </li>
                    </ul>
                    <hr class="dotted short">
                    <div class="text-end">
                        <a class="text-uppercase text-muted" href="#">(View All)</a>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="input-group">
                    <input type="text" class="form-control" name="s" id="s" placeholder="Search...">
                    <button class="btn btn-default" type="submit"><i class="bx bx-search"></i></button>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12 col-xl-6">
        <section class="card">
            <header class="card-header card-header-transparent">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">Company Activity</h2>
            </header>
            <div class="card-body">
                <div class="timeline timeline-simple mt-3 mb-3">
                    <div class="tm-body">
                        <div class="tm-title">
                            <h5 class="m-0 pt-2 pb-2 text-dark font-weight-semibold text-uppercase">November 2023</h5>
                        </div>
                        <ol class="tm-items">
                            <li>
                                <div class="tm-box">
                                    <p class="text-muted mb-0">7 months ago.</p>
                                    <p>
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas hendrerit augue at leo viverra, aliquam egestas lectus laoreet. Donec vehicula vestibulum ipsum, tincidunt ultrices elit suscipit ac. Sed eget risus laoreet, varius nibh id, luctus ligula. Nulla facilisi. <span class="text-primary">#awesome</span>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <div class="tm-box">
                                    <p class="text-muted mb-0">7 months ago.</p>
                                    <p>
                                        Checkout! How cool is that! Etiam efficitur, sapien eget vehicula gravida, magna neque volutpat risus, vitae tempus odio arcu ac elit. Aenean porta orci eu mi fermentum varius. Curabitur ac sem at nibh egestas. Curabitur ac sem at nibh egestas.
                                    </p>
                                    <div class="thumbnail-gallery">
                                        <a class="img-thumbnail lightbox" href="{{asset('porto-assets/img/projects/project-4.jpg')}}" data-plugin-options='{ "type":"image" }'>
                                            <img class="img-fluid" width="215" src="{{asset('porto-assets/img/projects/project-4.jpg')}}">
                                            <span class="zoom">
                                                <i class="bx bx-search"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row pt-4 mt-1">
    <div class="col-xl-6">
        <section class="card card-transparent mb-3">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">Global Stats</h2>
            </header>
            <div class="card-body">
                <div id="vectorMapWorld" style="height: 350px; width: 100%;"></div>
            </div>
        </section>
    </div>
    <div class="col-xl-6">
        <section class="card">
            <header class="card-header card-header-transparent">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                </div>

                <h2 class="card-title">Projects Stats</h2>
            </header>
            <div class="card-body">
                <table class="table table-responsive-md table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Porto - Responsive HTML5 Template</td>
                            <td><span class="badge badge-success">Success</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                        100%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Porto - Responsive Drupal 7 Theme</td>
                            <td><span class="badge badge-success">Success</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                        100%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Tucson - Responsive HTML5 Template</td>
                            <td><span class="badge badge-warning">Warning</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                        60%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Tucson - Responsive Business WordPress Theme</td>
                            <td><span class="badge badge-success">Success</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 90%;">
                                        90%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Porto - Responsive Admin HTML5 Template</td>
                            <td><span class="badge badge-warning">Warning</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 45%;">
                                        45%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Porto - Responsive HTML5 Template</td>
                            <td><span class="badge badge-danger">Danger</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded m-0 mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">
                                        40%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Porto - Responsive Drupal 7 Theme</td>
                            <td><span class="badge badge-success">Success</span></td>
                            <td>
                                <div class="progress progress-sm progress-half-rounded mt-1 light">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 95%;">
                                        95%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<!-- end: page -->

@endsection
@section('page-js')
@endsection
@section('scripts')
@endsection

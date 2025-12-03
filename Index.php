<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login");
    exit;
}
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Inventory</title>
  <!-- CSS dependencies -->
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
  <div class="container-scroller">
    <!--Navbar-->
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <!-- Theme Settings Panel -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="typcn typcn-cog-outline"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close typcn typcn-delete-outline"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border mr-3"></div>
            Light
          </div>
          <div class="sidebar-bg-options selected" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border mr-3"></div>
            Dark
          </div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles primary"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default border"></div>
          </div>
        </div>
      </div>
      <!--Sidebar-->
      <?php include 'sidebar.php'; ?>
<!--Main Panel-->
      <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0 font-weight-bold"></h3>
                <p>Your last login: 21h ago.</p>
              </div>
              <div class="col-sm-6">
                <div class="d-flex align-items-center justify-content-md-end">
                  <div class="mb-3 mb-xl-0 pr-1">
                      <div class="dropdown">
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuSizeButton3" data-x-placement="top-start">
                          <h6 class="dropdown-header">Last 14 days</h6>
                          <a class="dropdown-item" href="#">Last 21 days</a>
                          <a class="dropdown-item" href="#">Last 28 days</a>
                        </div>
                      </div>
                  </div>
                  <div class="pr-1 mb-3 mr-2 mb-xl-0">
                    <button type="button" class="btn btn-sm bg-white btn-icon-text border"><i class="typcn typcn-arrow-forward-outline mr-2"></i>Export</button>
                  </div>
                  <div class="pr-1 mb-3 mb-xl-0">
                    <button type="button" class="btn btn-sm bg-white btn-icon-text border"><i class="typcn typcn-info-large-outline mr-2"></i>info</button>
                  </div>
                </div>
              </div>
            </div>
            <!--first row-->
            <div class="row  mt-3">
              <!-- Active Items -->
              <div class="col-xl-5 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                      <h4 class="card-title mb-3">Active Items</h4>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-lg-6">
                            <div id="circleProgress6" class="progressbar-js-circle rounded p-3"></div>
                          </div>
                          <div class="col-lg-6">
                            <ul class="session-by-channel-legend">
                              <li>
                                <div>Firewalls(3)</div>
                                <div>4(100%)</div>
                              </li>
                              <li>
                                <div>Ports(12)</div>
                                <div>12(100%)</div>
                              </li>
                              <li>
                                <div>Servers(233)</div>
                                <div>2(100%)</div>
                              </li>
                              <li>
                                <div>Firewalls(3)</div>
                                <div>7(100%)</div>
                              </li>
                              <li>
                                <div>Firewalls(3)</div>
                                <div>6(70%)</div>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Event Statistics -->
              <div class="col-xl-4 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                      <h4 class="card-title mb-3">Events</h4>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="d-flex justify-content-between mb-md-5 mt-3">
                              <div class="small">Critical</div>
                              <div class="text-danger small">Error</div>
                              <div  class="text-warning small">Warning</div>
                            </div>
                            <canvas id="eventChart"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Warehouse stats -->
              <div class="col-xl-3 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                      <h4 class="card-title mb-3">Warehouse stats</h4>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="d-flex justify-content-between mb-4">
                              <div>Total Items</div>
                              <div class="text-muted">530</div>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                              <div>Low Stock Items</div>
                              <div class="text-muted">28</div>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                              <div>Last Restock</div>
                              <div class="text-muted">28 Jun 2025</div>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                              <div>Warehouse space</div>
                              <div class="text-muted">80%</div>
                            </div>
                            <div class="progress progress-md mt-4">
                              <div class="progress-bar bg-warning" role="progressbar" style="width: 80%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--second row-->
            <div class="row">
             <!-- Sales Products -->
              <div class="col-xl-5 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                      <h4 class="card-title mb-3">Sales Analytics</h4>
                      <button type="button" class="btn btn-sm btn-light">Month</button>
                    </div>
                    <canvas id="pieChart" width="100" height="100"></canvas>
                  </div>
                </div>
              </div>
              <!--end Sales Products -->
              <!--Sale Graph-->
              <div class="col-lg-4 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <canvas id="barChart" height="400"></canvas>
                  </div>                        
                </div>
              </div>
              <!--end of Graph-->
              <!-- Sales Summary -->
              <div class="col-xl-3 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                      <h4 class="card-title mb-3">Total Sales</h4>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-5">
                          <div class="mr-1">
                            <div class="text-info mb-1">
                              Total Earning
                            </div>
                            <h2 class="mb-2 mt-2 font-weight-bold" id="currentTotal"></h2>
                            <div class="font-weight-bold" id="currentGrowth">
                              ...% Since Last Month
                            </div>
                          </div>
                          <hr>
                          <div class="mr-1">
                            <div class="text-info mb-1">
                              Total Earning
                            </div>
                            <h2 class="mb-2 mt-2  font-weight-bold" id="previousTotal"></h2>
                              <div class="font-weight-bold" id="previousGrowth">
                              Last Year's Same Period
                            </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <!--end Sales Summary -->
            </div>
            <!--third row-->
            <div class="row">
                <div class="col-lg-12 d-flex grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="container mt-5">
                    <div class="analytics-card">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="analytics-title">Sales Analytics</h5>
                        <button class="btn btn-light btn-sm">Month</button>
                      </div>

                      <div class="row text-center mb-4">
                        <div class="col">
                          <span class="legend-icon text-danger">&#9792;</span>
                          <span>Online</span><br>
                          <span id="onlineTotal" class="legend-value text-danger">...</span>
                        </div>
                        <div class="col">
                          <span class="legend-icon text-primary">&#128187;</span>
                          <span>Offline</span><br>
                          <span id="offlineTotal" class="legend-value text-primary">...</span>
                        </div>
                        <div class="col">
                          <span class="legend-icon text-warning">&#128241;</span>
                          <span>Marketing</span><br>
                          <span id="marketingTotal" class="legend-value text-warning">...</span>
                        </div>
                      </div>
                        <canvas id="salesChart" height="100"></canvas>
                    </div>
                    </div>                     
                  </div>
                </div>
              </div>
            </div>
            <!--end of third row-->
          </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="#">randolfh.com</a> 2025</span>
          </div>
        </footer>
      </div>
      <!--end of Main Panel-->
    </div>
  </div>

  <!-- JS dependencies -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <script src="vendors/progressbar.js/progressbar.min.js"></script>
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="dashboard.js"></script>
</body>
</html>

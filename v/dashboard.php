<?php
require_once __DIR__ . '/../components/mainhead.php';

if(!isset($_SESSION['userid'])) {
    $utility_instance->revokeUnauthorize();
}

?>
<?php include_once GUEST_COMPONENT_DIR.'navbar.php';?>
<!-- Main Layout -->
<div class="container-fluid" style="padding-top: 80px;"> <!-- Adjust padding for navbar -->
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block nysc-green sidebar py-4">
            <div class="position-sticky">
                <ul class="nav flex-column text-white">
                    <li class="nav-item mb-3"><strong>Dashboard</strong></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">🏠 Dashboard</a></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">📢 Camp Updates</a></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">🏢 PPA Info</a></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">🔄 Redeployment</a></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">📊 Skills Tracker</a></li>
                    <li><a class="nav-link text-white sidebar-link" href="#">💬 Messages</a></li>
                    <li><a class="nav-link text-dark sidebar-link" href="logout">Log Out (<?php echo $userData['users_fname'];?>)</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <?php var_export($userData);?>
                    <h1 class="h3">Welcome, Corper <?php echo $userData['users_fname'];?> 👋</h1>
                    <p class="text-muted">Monday, July 21, 2025</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <img src="https://i.pravatar.cc/40" alt="Profile" class="rounded-circle">
                    <button class="btn btn-success">Edit Profile</button>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p class="text-muted">Days in Camp</p>
                            <h3 class="text-success fw-bold">12</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p class="text-muted">PPA Assigned</p>
                            <h5>General Hospital, Ikeja</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p class="text-muted">SAED Track</p>
                            <h5>Catering (75%)</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p class="text-muted">Monthly Allowance</p>
                            <h4 class="text-success fw-bold">₦33,000</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Camp Schedule Table -->
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <h5 class="card-title mb-4">📅 Today’s Camp Schedule</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-success">
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>Venue</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>06:00 AM</td>
                                    <td>Parade & Drill</td>
                                    <td>Parade Ground</td>
                                    <td><span class="badge bg-success">Done</span></td>
                                </tr>
                                <tr>
                                    <td>09:00 AM</td>
                                    <td>SAED Training</td>
                                    <td>Hall 2</td>
                                    <td><span class="badge bg-warning text-dark">Ongoing</span></td>
                                </tr>
                                <tr>
                                    <td>03:00 PM</td>
                                    <td>Team Sports</td>
                                    <td>Football Field</td>
                                    <td><span class="badge bg-secondary">Upcoming</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include_once GUEST_COMPONENT_DIR.'footer.php';?>
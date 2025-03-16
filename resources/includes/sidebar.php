<div  class="sidebar-wrapper">
    <div id="sidebarEffect"></div>
    <div style="background-color: #007587;margin:0;">
        <?php
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Get user accessibility
        $user_accessibility = $_SESSION['user']['accessibility'];
        $is_super_admin = isset($user_accessibility['super_admin']) && $user_accessibility['super_admin'] == true;
        ?>

        <div class="logo-wrapper logo-wrapper-center">
            <a>
                <!--<img style="height: 45px; width: auto;" class="img-fluid main-logo" src="assets/images/logo/1.png" alt="logo">-->
                <b style="color:white">ANALYSIS</b>
            </a>

            <div class="back-btn">
                &nbsp;&nbsp;&nbsp; <i class="fa fa-angle-left"></i>
            </div>
            <div class="toggle-sidebar">
                <i class="ri-apps-line status_toggle middle sidebar-toggle"></i>
            </div>
        </div>

        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow">
                <i data-feather="arrow-left"></i>
            </div>

            <div id="sidebar-menu">

                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"></li>

                    <!-- Dashboard (Accessible to all users) -->
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="index">
                            <i class="ri-home-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Administrative Users -->
                    <?php if ($is_super_admin || isset($user_accessibility['all_roles']) || isset($user_accessibility['add_new_role']) || isset($user_accessibility['all_users']) || isset($user_accessibility['add_new_user'])): ?>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-user-settings-fill"></i>
                            <span>Administrative Users</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <?php if ($is_super_admin || isset($user_accessibility['all_roles'])): ?>
                            <li>
                                <a href="roles">All Roles</a>
                            </li>
                            <?php endif; ?>
                            <?php if ($is_super_admin || isset($user_accessibility['add_new_role'])): ?>
                            <li>
                                <a href="add-role">Add new role</a>
                            </li>
                            <?php endif; ?>
                             <?php if ($is_super_admin || isset($user_accessibility['all_users'])): ?>
                            <li>
                                <a href="users">All Users</a>
                            </li>
                            <?php endif; ?>
                            <?php if ($is_super_admin || isset($user_accessibility['add_new_user'])): ?>
                            <li>
                                <a href="add-user">Add new user</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Inputs -->
                    <?php if ($is_super_admin || isset($user_accessibility['all_inputs']) ||  isset($user_accessibility['all_expenses'])  ||  isset($user_accessibility['all_revenue'])  ): ?>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-upload-cloud-line"></i>
                            <span>Inputs</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <?php if ($is_super_admin || isset($user_accessibility['add_expenses'])): ?>
                            <li>
                                <a href="expenses-add"> Add Expenses</a>
                            </li>
                            <?php endif; ?>
                            <?php if ($is_super_admin || isset($user_accessibility['add_revenue'])): ?>
                            <li>
                                <a href="revenue-add">Add Revenue</a>
                            </li>
                            <?php endif; ?>
                             <?php if ($is_super_admin || isset($user_accessibility['add_revenue'])): ?>
                            <li>
                                <a href="neglected-revenue-add"> Add Income Revenue</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Analysis -->
                    <?php if ($is_super_admin || isset($user_accessibility['all_analysis']) || isset($user_accessibility['expenses'])  || isset($user_accessibility['revenue'])  || isset($user_accessibility['profit-loss'])  ): ?>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-bar-chart-line"></i>
                            <span>Analysis</span>
                        </a>
                        <ul class="sidebar-submenu">
                          
                              <?php if ($is_super_admin || isset($user_accessibility['expenses'])): ?>
                            <li>
                                <a href="expenses-5">Expenses</a>
                            </li>
                            <?php endif; ?>
                               <?php if ($is_super_admin || isset($user_accessibility['expenses'])): ?>
                            <li>
                                <a href="expenses-4">Hybrid Expenses</a>
                            </li>
                            <?php endif; ?>
                          
                            <?php if ($is_super_admin || isset($user_accessibility['revenue'])): ?>
                            <li>
                                <a href="revenue">Revenue</a>
                            </li>
                            <?php endif; ?>
                            <?php if ($is_super_admin || isset($user_accessibility['revenue'])): ?>
                            <li>
                                <a href="income-revenue">Income Only Revenue</a>
                            </li>
                            <?php endif; ?>
                             <?php if ($is_super_admin || isset($user_accessibility['revenue'])): ?>
                            <li>
                                <a href="withdrawals">Withdrawn Only Revenue</a>
                            </li>
                            <?php endif; ?>
                             <?php if ($is_super_admin || isset($user_accessibility['profit-loss'])): ?>
                            <li>
                                <a href="profit-loss">Profit-Loss</a>
                            </li>
                            <?php endif; ?>
                            
                            
                        </ul>
                    </li>
                    <?php endif; ?>

              

                    <!-- Reports -->
                    <?php if ($is_super_admin || isset($user_accessibility['reports'])): ?>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="reports">
                            <i class="ri-file-chart-line"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>

            <div class="right-arrow" id="right-arrow">
                <i data-feather="arrow-right"></i>
            </div>

        </nav>
    </div>

</div>

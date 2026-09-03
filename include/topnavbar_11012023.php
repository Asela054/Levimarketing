<?php 
$sessionusertype=$_SESSION['type'];

$sqlusertype="SELECT `type` FROM `tbl_user_type` WHERE `idtbl_user_type`='$sessionusertype' AND `status`=1";
$resultusertype =$conn-> query($sqlusertype);
$rowusertype = $resultusertype-> fetch_assoc();
?>
<nav class="topnav navbar navbar-expand shadow navbar-light bg-laugfs" id="sidenavAccordion">
    <a class="navbar-brand d-none d-sm-block menu-logo" href="#">Lionel Trade Center</a><?php if($_SESSION['postype']==0){ ?><button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 mr-lg-2" id="sidebarToggle" href="#"><i class="text-dark" data-feather="menu"></i></button><?php } ?>
    <ul class="navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown no-caret mr-3 dropdown-user">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-user text-dark"></i></a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <img class="dropdown-user-img" src="<?php if($_SESSION['image']!=''){echo $_SESSION['image'];}else{echo 'images/user.jpg';} ?>" />
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name"><?php echo ucfirst($_SESSION['name']); ?></div>
                        <div class="dropdown-user-details-email"><?php echo $rowusertype['type']; ?></div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
<!--
                <a class="dropdown-item" href="#!">
                    <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                    Account
                </a>
-->
                <a class="dropdown-item" href="process/logoutprocess.php">
                    <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

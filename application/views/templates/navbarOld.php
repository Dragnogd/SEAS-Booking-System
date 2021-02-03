<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">
        <img src="<?php echo base_url() . "public/images/ashLogo.png";?>" width="35" height="28" class="d-inline-block align-top" alt="">
        IT Bookings
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo site_url("home/index"); ?>">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url("bookequipment/index"); ?>">Book Equipment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url("bookroom/index"); ?>">Book Room/Assembly (IT assistance)</a>
            </li>
        </ul>
        <span class="navbar-text">
            <?php echo $_SESSION['forename'] . " " . $_SESSION['surname'] ?>
            <a href="<?php echo site_url("home/Logout"); ?>">(Logout)</a>
        </span>
    </div>
</nav>
<br>
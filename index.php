<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section text-center text-white" style="background: linear-gradient(135deg, #6FCF97, #56CCF2); padding: 120px 0;">
    <div class="container">
        <h1 class="display-4 fw-bold">Welcome to ServeSmart</h1>
        <p class="lead mb-4">Connecting Food Distributors with NGOs to Reduce Food Waste</p>

        <?php if(!isset($_SESSION['user'])): ?>
            <a href="login.php" class="btn btn-light btn-lg m-2">Login</a>
            <a href="register.php" class="btn btn-outline-light btn-lg m-2">Register</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-warning btn-lg m-2">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>



<!-- ABOUT US SECTION WITH SIDE IMAGE -->
<section class="py-5" style="background: #F0F8FF;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Text Column -->
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="mb-4 fw-bold text-primary">About Us</h2>
                <p class="lead">
                    ServeSmart is a web-based platform that reduces food wastage by connecting food distributors with NGOs.
                    We ensure efficient food distribution while helping those in need.
                </p>
                <a href="register.php" class="btn btn-gradient mt-3" style="background: linear-gradient(135deg,#56CCF2,#6FCF97); color:white;">Get Started</a>
            </div>

            <!-- Image Column -->
            <div class="col-md-6">
                <img src="assets/images/about-hero.png" 
                     class="img-fluid rounded shadow" 
                     alt="ServeSmart About Us">
            </div>
        </div>
    </div>
</section>

<!-- OUR SERVICES SECTION -->
<section class="py-5" style="background: linear-gradient(90deg,#FFAFBD,#ffc3a0); color:white;">
    <div class="container text-center">
        <h2 class="mb-5 fw-bold">Our Services</h2>
        <div class="row g-4">

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 p-3" style="background:#FFFFFF; color:#333;">
                    <div class="card-body">
                        <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title fw-bold">Food Distribution</h5>
                        <p class="card-text">Post available food items and allow NGOs to pick up in real-time, reducing wastage efficiently.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 p-3" style="background:#FFFFFF; color:#333;">
                    <div class="card-body">
                        <i class="fas fa-utensils fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title fw-bold">Auto Expiry Management</h5>
                        <p class="card-text">Automatically marks food items as expired after their time limit, ensuring only fresh and safe food is available for NGOs.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 p-3" style="background:#FFFFFF; color:#333;">
                    <div class="card-body">
                        <i class="fas fa-hands-helping fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title fw-bold">Community Support</h5>
                        <p class="card-text">Help NGOs serve communities efficiently while reducing food wastage in society.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="py-5" style="background: #E0F7FA;">
    <div class="container text-center">
        <h2 class="mb-5 fw-bold text-primary">What Our Users Say</h2>
        <div class="row justify-content-center g-4">

            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0 p-4" style="background:#FFFFFF;">
                    <div class="card-body">
                        <p class="card-text fst-italic">"ServeSmart helped us reach needy people faster and manage food distribution seamlessly!"</p>
                        <h6 class="mt-3 fw-bold text-primary">- NGO Volunteer</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0 p-4" style="background:#FFFFFF;">
                    <div class="card-body">
                        <p class="card-text fst-italic">"As a distributor, I love how I can post extra food and reduce wastage effectively."</p>
                        <h6 class="mt-3 fw-bold text-primary">- Food Distributor</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0 p-4" style="background:#FFFFFF;">
                    <div class="card-body">
                        <p class="card-text fst-italic">"This platform connects NGOs and distributors efficiently. Highly recommended!"</p>
                        <h6 class="mt-3 fw-bold text-primary">- Community Member</h6>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>




<script>
function searchFood() {

    let search = document.getElementById("search").value;
    let location = document.getElementById("location").value;
    let min_price = document.getElementById("min_price").value || 0;
    let max_price = document.getElementById("max_price").value || 999999;
    let sort = document.getElementById("sort").value;

    let loader = document.getElementById("loader");
    let results = document.getElementById("results");

    loader.style.display = "block";
    results.innerHTML = "";

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "api/search_food.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        loader.style.display = "none";

        if (this.status === 200) {
            results.innerHTML = this.responseText;
        } else {
            results.innerHTML = "<p style='color:red;'>Error loading results</p>";
        }
    };

    xhr.send(
        "search=" + encodeURIComponent(search) +
        "&location=" + encodeURIComponent(location) +
        "&min_price=" + encodeURIComponent(min_price) +
        "&max_price=" + encodeURIComponent(max_price) +
        "&sort=" + encodeURIComponent(sort)
    );
}

// 🔥 Auto search when typing (debounce)
let timer;
document.getElementById("search").addEventListener("keyup", function () {
    clearTimeout(timer);
    timer = setTimeout(searchFood, 500);
});

// 🔥 Trigger search on filter change
document.getElementById("location").addEventListener("change", searchFood);
document.getElementById("min_price").addEventListener("change", searchFood);
document.getElementById("max_price").addEventListener("change", searchFood);
document.getElementById("sort").addEventListener("change", searchFood);
</script>

<?php include 'includes/footer.php'; ?>
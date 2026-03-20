// =========================
// GLOBAL READY FUNCTION
// =========================
$(document).ready(function(){

    console.log("ServeSmart Loaded ✅");

    // Auto-hide alerts
    setTimeout(function(){
        $(".alert").fadeOut();
    }, 3000);

});


// =========================
// ADD TO CART (AJAX)
// =========================
function addToCart(id) {
    $.post('/ServeSmart/api/add_to_cart.php', {id: id}, function(res){
        let data = JSON.parse(res);

        alert("Item added to cart 🛒");

        updateCartCount(data.cart_count);
    });
}


// =========================
// UPDATE CART COUNT BADGE
// =========================
function updateCartCount(count) {
    $("#cartCount").text(count);
}


// =========================
// FETCH FOOD (OPTIONAL FILTER)
// =========================
function fetchFood(location = '') {

    $.get('/ServeSmart/api/fetch_food.php?location=' + location, function(res){
        let foods = JSON.parse(res);

        let html = '';

        foods.forEach(food => {
            html += `
                <div class="col-md-4">
                    <div class="card p-3 mb-3 shadow-soft">
                        <h5>${food.food_name}</h5>
                        <p>₹ ${food.price}</p>
                        <p>${food.location}</p>

                        <button class="btn btn-success"
                            onclick="addToCart(${food.id})">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
        });

        $("#foodContainer").html(html);
    });
}


// =========================
// PLACE ORDER (AJAX)
// =========================
function placeOrder() {
    $.post('/ServeSmart/api/place_order.php', function(res){
        let data = JSON.parse(res);

        if (data.status === "success") {
            alert("Order Placed Successfully ✅");
            window.location.href = "orders.php";
        } else {
            alert(data.message);
        }
    });
}


// =========================
// CONFIRM DELETE
// =========================
function confirmDelete() {
    return confirm("Are you sure you want to delete?");
}


// =========================
// LIVE SEARCH (OPTIONAL)
// =========================
$("#searchInput").on("keyup", function(){
    let value = $(this).val();
    fetchFood(value);
});
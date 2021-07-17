// Processus de paiement Stripe

var stripe = Stripe("YOUR STRIPE KEY");
var checkoutButton = document.getElementById("checkout-button");
checkoutButton.addEventListener("click", function () {
  fetch("/paiement/"+checkoutButton.getAttribute('data-reference'), {
    method: "POST",
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (session) {
      return stripe.redirectToCheckout({ sessionId: session.id });
    })
    .then(function (result) {
      if (result.error) {
        alert(result.error.message);
      }
    })
    .catch(function (error) {
      console.error("Error:", error);
    });
});

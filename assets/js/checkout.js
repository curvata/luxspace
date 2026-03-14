// Processus de paiement Stripe

var checkoutButton = document.getElementById("checkout-button");
var stripe = Stripe(checkoutButton.getAttribute("data-stripe-key"));

checkoutButton.addEventListener("click", function () {
  fetch("/paiement/" + checkoutButton.getAttribute('data-reference'), {
    method: "POST",
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      if (data.erreur) {
        alert("Erreur : " + data.erreur);
        return;
      }
      return stripe.redirectToCheckout({ sessionId: data.id });
    })
    .then(function (result) {
      if (result && result.error) {
        alert(result.error.message);
      }
    })
    .catch(function (error) {
      console.error("Erreur Stripe:", error);
      alert("Une erreur est survenue, veuillez réessayer.");
    });
});

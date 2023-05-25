<script src="https://js.stripe.com/v3/"></script>
<script src="modules/CMSMSStripe/javascript/utils.js"></script>
{**if isset($_stripe_message)**}
	<p class="alert alert-danger">{**$_stripe_message**}</p>
{**/if**}
<main>
      <a href="/">home</a>
      <h1>Card</h1>
      <p>
        <h4>Try a <a href="https://stripe.com/docs/testing#cards" target="_blank">test card</a>:</h4>
        <div>
          <code>4242424242424242</code> (Visa)
        </div>
        <div>
          <code>5555555555554444</code> (Mastercard)
        </div>
        <div>
          <code>4000002500003155</code> (Requires <a href="https://www.youtube.com/watch?v=2kc-FjU2-mY" target="_blank">3DSecure</a>)
        </div>
      </p>
    {**if $_stripe_client_secret**}<div class="alert alert-success">{**$_stripe_client_secret**}</div>{**/if**}
      <p>
        Use any future expiration, any 3 digit CVC, and any postal code.
      </p>

      <form id="payment-form">
      <div id="link-authentication-element">
      <!--Stripe.js injects the Link Authentication Element-->
    </div>
        <div id="payment-element">
          <!-- Elements will create input elements here -->
        </div>
        <button id="submit" class="btn btn-primary">
        <div class="spinner hidden" id="spinner"></div>
        <span id="button-text">Pay now</span>
      </button>
      </form>

      <div id="payment-message" class="hidden mt-3 alert alert-warning"></div>
    </main>


<script>

        const stripe = Stripe( "{**$cmsms_stripe->publishable_key**}" );

        let elements;
        let emailAddress = '';

        initialize();
        checkStatus();

        document.querySelector("#payment-form").addEventListener("submit", handleSubmit);

        // Fetches a payment intent and captures the client secret
        async function initialize() {
          
            const data = { price: "{**$cmsms_stripe->amount**}" };

            const { clientSecret } = await fetch("{**cms_action_url action="create-checkout"**}", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ data }),
              }).then((r) => r.json());

              const appearance = {
                theme: '{**$cmsms_stripe->theme**}'
              };

              elements = stripe.elements({clientSecret,appearance});

              const linkAuthenticationElement = elements.create("linkAuthentication");
              linkAuthenticationElement.mount("#link-authentication-element");

              const paymentElementOptions = {
                layout: "tabs",
              };

              const paymentElement = elements.create("payment", paymentElementOptions);
              paymentElement.mount("#payment-element");

        }

        async function handleSubmit(e) {
          e.preventDefault();
          setLoading(true);

          const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
              return_url: "{**$cmsms_stripe->url_cancel**}",
            },
          });

          // This point will only be reached if there is an immediate error when
          // confirming the payment. Otherwise, your customer will be redirected to
          // your `return_url`. For some payment methods like iDEAL, your customer will
          // be redirected to an intermediate site first to authorize the payment, then
          // redirected to the `return_url`.
          if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
          } else {
            showMessage("An unexpected error occurred.");
          }

          setLoading(false);
        }

        // Fetches the payment intent status after payment submission
        async function checkStatus() {
          const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
          );

          if (!clientSecret) {
            return;
          }

          const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

          switch (paymentIntent.status) {
            case "succeeded":
              showMessage("Payment succeeded!");
              break;
            case "processing":
              showMessage("Your payment is processing.");
              break;
            case "requires_payment_method":
              showMessage("Your payment was not successful, please try again.");
              break;
            default:
              showMessage("Something went wrong.");
              break;
          }
        }
</script>
{**get_template_vars**}
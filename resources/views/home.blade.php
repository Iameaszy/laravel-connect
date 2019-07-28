@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(Auth::user()->category === 'seller')
                <div class="card-header">Seller Dashboard</div>
                @else
                <div class="card-header">Dashboard</div>
                @endif
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    You are logged in!
                    @if(Auth::user()->category === 'seller')
                    <div class="row d-flex justify-content-center mt-5">
                        @if(!Auth::user()->connect_id)
                        <a class="btn btn-primary"
                            href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_FU2UlUuX6Kt8HRxlTOhVgMNP0Ec7bBxl&scope=read_write">Connect
                            with Stripe</a>
                        @else
                                <h1 class="text-success">You are connected</h1>
                        @endif

                    </div>
                    @else
                    <h3 class="text-center mb-4">Checkout Details</h3>
                    <form method="POST" action="{{ route('charge') }}" id="payment-form">
                        @csrf
                        <div class="form-group row">
                            <label for="card-element" class="col-md-4 col-form-label text-md-right">
                                Credit or debit card
                            </label>

                            <div class="col-md-6">
                                <div id="card-element">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>

                                <!-- Used to display Element errors. -->
                                <div id="card-errors" role="alert"></div>
                            </div>
                        </div>

                        <div class="form-group row">

                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Amount') }}</label>
                            <input type="hidden" name="token" value="">
                            <div class="col-md-6">
                                <input id="amount" type="number"
                                    class="form-control @error('amount') is-invalid @enderror" name="amount"
                                    value="{{ old('amount') }}" required autocomplete="amount" min="5" value="5"
                                    autofocus>

                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Checkout') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($error))
<!-- The Modal -->
<div class="modal fade" id="myModal">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title text-danger">ERROR</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              {{$error_description}}
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

          </div>
        </div>
      </div>
@endif
@if(isset($stripe_user_id))
<!-- The Modal -->
<div class="modal fade" id="myModal2">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title text-success">SUCCESS, CONNECTED!</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <p class="text">Your stripe acount was connected successfully!</p>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

          </div>
        </div>
      </div>
@endif
@if(isset($payment_id))
<!-- The Modal -->
<div class="modal fade" id="paymentForm">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title text-success">Payment Successful!</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <p class="text">Transaction with id {{$payment_id}} occured and {{(int)$amount/100}} was deposited to your stripe account</p>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

          </div>
        </div>
      </div>
@endif
@endsection
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe("{{env('STRIPE_TEST_PULISHABLE_KEY')}}");
    var elements = stripe.elements();
    var style = {
  base: {
    color: '#32325d',
    lineHeight: '18px',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

card.addEventListener('change', function(event) {
  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
});
var $paymentForm = $("#payment-form");
$paymentForm.on('submit',function(e){
    e.preventDefault();
    var $that = this;
    stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the customer that there was an error.
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server.
      //stripeTokenHandler(result.token);
      $paymentForm.find('[name="token"]').val(result.token.id);
      //console.log($($that).serialize());
      $that.submit();
    }
  });
})
</script>
<script>
$("#myModal").modal('show');
$("#myModal2").modal('show');
$("#paymentForm").modal('show');
const urlParams = new URLSearchParams(window.location.search);
const myParam = urlParams.get('error')||urlParams.get('code')||urlParams.get('payment_id')
if(myParam){
    setTimeout(function(){
        location.replace("http://localhost:8000/home");
    },4000);
}
</script>
@endsection

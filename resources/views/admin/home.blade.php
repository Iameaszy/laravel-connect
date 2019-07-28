@extends('layouts.admin')

@section('css')
<style>
    .y-pt {
        padding-top: 3.2rem;
    }

    .selected{
        background: #e4e4e4;
    }
    .my-list-group-item {
        border: 0;
        border-radius: 0;
        box-shadow: none;
        padding: 3px 0.5rem !important;
        padding-left: 1rem !important;
        border-bottom: 1px solid #e0dede;
        cursor: pointer;
        margin-bottom: 10px;
    }

    .my-list-group-item:hover{
        background: #e4e4e4;
    }

    .my-list-group-wrapper {}

    .my-list-group {
        background: #f5f5f5;
        min-height: 10rem;
        padding-top: 0.5rem;
        overflow-y: scroll;
    }
    .error-feedback{
        display: none;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Status</th>
                        <th scope="col">Seller</th>
                        <th scope="col">Operation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <th scope="row">{{$payment->id}}</th>
                        <td>{{$payment->amount}}</td>
                        <td>{{$payment->buyer[0]['name']}}</td>
                        <td>{{$payment->buyer[0]['email']}}</td>
                        <td>
                            @if($payment->seller_id === null)
                            Not assigned to a seller
                            @else
                            Assigned to a seller
                            @endif
                        </td>
                        <td>
                            @if($payment->seller_id === null)
                            Null
                            @else
                            {{$payment->seller[0]['email']}}
                            @endif
                        </td>
                        <td>
                            @if($payment->seller_id === null)
                        <p class="text-center"><button class="btn btn-sm btn-success assign-modal-toggle-btn" data-id="{{$payment->id}}"
                         type="button">Assign</button></p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal fade" id="serviceModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Assign Payment to a seller</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body container-fluid">
                <form method="POST" id="assign-form" action={{route('assign')}}>
                @csrf
                        <input id="seller" type="hidden" name="seller_id">
                        <input id="amount"  type="hidden" name="amount">
                        <input id="payment"  type="hidden" name="payment_id" />
                </form>
                <div class="row assign-form" method="POST" action={{route('assign')}} >
                    <div class="col-5 my-list-group-wrapper">
                        <h1>Select a seller</h1>
                        <ul class="list-group my-list-group">
                            @foreach($sellers as $seller)
                            <li data-id="{{$seller->id}}" class="list-group-item my-list-group-item">{{$seller->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-5 y-pt">
                        <input id="pre-amount" type="text" class="form-control" placeholder="Amount" />
                        <div class="alert alert-danger error-feedback p-0 pl-2"></div>
                        <p class="text-right">
                                <button type="button" class="btn mt-2 btn-outline-success assign-btn">Assign</button>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    (function($){
            $('.my-list-group-item:not(.selected)').click(function(){
                $(this).addClass("selected");
                $(this).siblings().removeClass("selected");
                $('#seller').val($(this).data('id'));
                $('.error-feedback').css('display','none');
            })
            $('#pre-amount').change(function(){
                $('.error-feedback').css('display','none');
            })
            $('.assign-btn').click(function(e){
                   var $errorFeedback = $('.error-feedback');
                   $errorFeedback.css('display','none');
                    var $seller = $('#seller').val();
                    var $preAmount = $('#pre-amount').val();
                    var $amount = $('#amount');
                    if(!$seller){
                        return $errorFeedback.css('display','block').text('No user selected')
                    }
                    if(!$preAmount){
                        return $errorFeedback.css('display','block').text('Please enter an amount')
                    }
                    if(isNaN(parseInt($preAmount)) ){
                        return $errorFeedback.css('display','block').text('Please enter integer amount')
                    }
                    $amount.val($preAmount);
                    $('#assign-form').submit();
            })
            $('.assign-modal-toggle-btn').click(function(e){
                $("#payment").val($(this).data('id'));
                $("#serviceModal").modal('show');
            })
    })(jQuery)

</script>
@endsection

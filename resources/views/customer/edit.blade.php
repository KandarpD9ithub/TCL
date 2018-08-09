@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="container-fluid">
                          <ul class="nav nav-tabs" id="myTabs">
                            <li class="active"><a  data-toggle="tab"href="#cutomer" data-url="/embed/62805/view">Customer</a></li>
                            @if(isset($band_details) and ($band_details != null))
                            <li><a data-toggle="tab" href="#band_details" data-url="/embed/62806/view">Band Details</a></li>
                            <li><a data-toggle="tab" href="#tcl_wallet" data-url="/embed/62807/view">TCL Wallet Information</a></li>

                            @endif
                          </ul>
                          
                          <div class="tab-content">
                            <div class="tab-pane active" id="cutomer">
                                
                            
                            
                    <div class="panel-body">
                        {{ Form::model($customer, ['route' => ['customer.update', 'id' => $customer->id], 'method' => 'put', 'id' => 'edit-customer','files'=>true]) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', null, ['class' => 'form-control','placeholder'=>Lang::get('views.name')]) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('contact_number') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('contact_number', Lang::get('views.contact_number'), ['class' => "control-label"]) }}
                                {{ Form::text('contact_number', null, ['class' => 'form-control keyNumSingle','placeholder'=>Lang::get('views.contact_number')]) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('contact_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('email', Lang::get('views.email'), ['class' => "control-label"]) }}
                                {{ Form::email('email', null, ['class' => 'form-control','placeholder'=>Lang::get('views.email')]) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif                                
                            </div>
                            <div class="form-group{{ $errors->has('profile_picture') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('profile_picture', Lang::get('views.profile_picture'), ['class' => "control-label"]) }}
                                {{ Form::file('profile_picture', ['class' => 'form-control','placeholder'=>Lang::get('views.profile_picture')]) }}
                                @if ($errors->has('profile_picture'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('profile_picture') }}</strong>
                                </span>
                                @endif
                                @if($customer->profile_picture != null)
                                    @if (file_exists(public_path().'/upload/'.$customer->profile_picture))
                                        <img src="{{URL::asset('upload')}}/{{$customer->profile_picture}}" width="50%" height="50%">
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('address_line_one') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_one'), ['class' => "control-label"]) }}
                                {{ Form::text('address_line_one', null, ['class' => 'form-control','placeholder'=>Lang::get('views.address_line_one')]) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_one') }}</strong>
                                </span>
                                @endif                                
                            </div>
                            <div class="form-group{{ $errors->has('address_line_two') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_two', Lang::get('views.address_line_two'), ['class' => "control-label"]) }}
                                {{ Form::text('address_line_two', null, ['class' => 'form-control','placeholder'=>Lang::get('views.address_line_two')]) }}
                                @if ($errors->has('address_line_two'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_two') }}</strong>
                                </span>
                                @endif                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('city', Lang::get('views.city'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('city', null, ['class' => 'form-control','placeholder'=>Lang::get('views.city')]) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('region', Lang::get('views.region'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('region', null, ['class' => 'form-control','placeholder'=>Lang::get('views.region')]) }}
                                @if ($errors->has('region'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('region') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('country_id', Lang::get('views.country'),
                                ['class' => "control-label"]) }}
                                {{ Form::select('country_id', $countries, null, ['class' => 'form-control','placeholder'=>Lang::get('views.select_country')]) }}
                                @if ($errors->has('country_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('country_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }} col-md-12">
                                {{ Form::label('email', Lang::get('views.comments'), ['class' => "control-label"]) }}
                                {{ Form::textarea('comments', null, ['class' => 'form-control','placeholder'=>Lang::get('views.comments'),'rows'=>2]) }}
                                @if ($errors->has('comments'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('comments') }}</strong>
                                </span>
                                @endif                                
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/customer', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                                {{ Form::submit(Lang::get('views.submit'), ['class' => 'btn btn-primary']) }}

                            {{--    @if(!isset($band_details) and ($band_details == null))
                                    {{ Form::button(Lang::get('views.issue_band_btn'), ['class' => 'btn btn-primary','onClick'=>'fun_popup("issue_band")']) }}
                                @endif
                            --}}

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div> <!-- End class panel panel-default -->
                <!-- band details -->
                 <div class="tab-pane" id="band_details">
                        <div class="panel-body">
                                @if(isset($band_details) and $band_details != null)<!-- check variable band_details is set and not null  -->
                                    <?php
                                    $nfc_number=$band_details->original_UUID;
                                    if (!empty($band_details->deleted_at)) {
                                        $returnDate = 
                                     Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $band_details->deleted_at, 'UTC')
                                       ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                                        $status=Lang::get('views.band_returned', ['date' => $returnDate]);  
                                    } else {
                                        if ($band_details->status == 1) {
                                        $status=Lang::get('views.new');//assign if status is 1
                                        }elseif ($band_details->status == 2) {
                                            $status=Lang::get('views.in_use');//assign if status is 2
                                        }elseif ($band_details->status == 3) {
                                            $status=Lang::get('views.dameged');//assign if status is 3
                                        }elseif ($band_details->status == 4) {
                                            $status=Lang::get('views.lost');//assign if status is 4
                                        }else{
                                            $status =null;//assign if status is null of wrong
                                        }
                                    }
                                    

                                    $date_of_issue=
                                     Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $band_details->issued_at, 'UTC')
                                       ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                                    ?>
                                @else
                                <?php
                                //assign all of null if status is null
                                    $nfc_number=null;
                                    $status=null;
                                    $date_of_issue=null;
                                    ?>
                                @endif
                            <div class="row">
                                 <div class="col-md-12">
                                <div class="col-md-4">
                                    {{ Form::label('nfc_number_id', Lang::get('views.nfc_number'), ['class' => "control-label"]) }}
                                </div>
                                <div class="col-md-6">
                                {{ Form::label('nfc_number',$nfc_number == null ? 'Not Found' : $nfc_number, ['class' => "control-label"]) }}
                                </div>
                                </div>
                               
                            </div>
                            <div class="row">
                                 <div class="col-md-12">
                                <div class="col-md-4">
                                    {{ Form::label('status_id', Lang::get('views.status'), ['class' => "control-label"]) }}
                                </div>
                                <div class="col-md-6">
                                    {{ Form::label('status',$status == null ? 'Not Found' : $status, ['class' => "control-label"]) }}
                                </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                <div class="col-md-4">
                                    {{ Form::label('date_of_issue_id', Lang::get('views.date_of_issue'), ['class' => "control-label"]) }}
                                </div>
                                <div class="col-md-6">
                                    {{ Form::label('date_of_issue',$date_of_issue == null ? 'Not Found' : $date_of_issue, ['class' => "control-label"]) }}
                                </div>
                                </div>
                            </div>
                            @if(isset($band_details) && $band_details != null && (empty($band_details->deleted_at) || $band_details->status == 3 || $band_details->status == 4))
                            {{ Form::open(['url'=>'customer/update_comment/'.$band_details->id]) }}
                            <div class="row">
                                <div class="col-md-12">
                                <div class="col-md-4">
                                    {{ Form::label('active_inactive_id', Lang::get('views.active_inactive'), ['class' => "control-label"]) }}
                                </div>
                                <div class="col-md-6">
                                    @if(isset($band_details) and $band_details != null and $band_details->is_active == 1)

                                        {{ Form::submit(Lang::get('views.active_btn'), ['class' => 'btn btn-success','onclick'=>'return confirm("Are you sure you want to InActive?")']) }}

                                    @elseif(isset($band_details) and $band_details != null and $band_details->is_active == 0)

                                        {{ Form::submit(Lang::get('views.inactive_btn'), ['class' => 'btn btn-danger','onclick'=>'return confirm("Are you sure you want to Active?")']) }}

                                    @endif
                                </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }} col-md-12">
                                {{ Form::label('email', Lang::get('views.comment'), ['class' => "control-label"]) }}
                                {{ Form::textarea('comment', $band_details->comment, ['class' => 'form-control','placeholder'=>Lang::get('views.comment'),'rows'=>2]) }}
                                @if ($errors->has('comment'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span>
                                @endif                                
                            </div>
                        </div>


                            <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/customer', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                            {{--
                                {{ Form::button(Lang::get('views.recharge'), ['class' => 'btn btn-primary','onClick'=>'fun_popup("recharge")']) }}
                                
                                {{ Form::button(Lang::get('views.reissue_band_btn'), ['class' => 'btn btn-primary','onClick'=>'fun_popup("issue_band")']) }}
                            --}}
                            </div>
                        </div>
                        {{ Form::close() }}
                        @endif
                        </div>


                        {{--
                        <!-- Linked Information -->

                            <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel panel-default">
                                
                                    <table class="table table-responsive">
                                    <tbody>
                                        <tr>
                                            <th>{{ Lang::get('views.transaction_id') }}</th>
                                            <th>{{ Lang::get('views.date') }}</th>
                                            <th>{{ Lang::get('views.debit_amount') }}</th>
                                            <th>{{ Lang::get('views.credit_amount') }}</th>
                                        </tr>
                                        @if(isset($customers_history) and (count($customers_history) > 0))
                                            @foreach($customers_history as $customer)
                                                <tr>
                                                    <td>
                                                        {{ ucfirst($customer->transaction_id) }}
                                                    </td><td>
                                                        {{date('d M, Y', strtotime($customer->created_at))}}
                                                    </td>
                                                    <td>
                                                        {{$customer->debit_amount}}
                                                    </td>
                                                    <td>
                                                        {{$customer->credit_amount}}
                                                    </td>
                                                
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="3" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="pull-right">
                                    @if(isset($customers_history))
                                    {{ $customers_history->links() }}
                                    @endif
                                </div>
                            </div> <!-- End class panel panel-default -->
                        </div> <!-- End class col-md-8 col-md-offset-2 -->
                    </div> <!-- End class row -->

                        <!-- end Linked information -->
                    --}}

                    </div>
                    <div class="tab-pane" id="tcl_wallet">
                        <div class="panel-body">
                            <div class="row">
                                @if(isset($tlc_wallet_info) and $tlc_wallet_info != null) <!-- check variable tlc_wallet_info is  set and not null -->
                                    <?php
                                    $current_balance = $tlc_wallet_info->balance_amount;
                                    $credit_amount = $tlc_wallet_info->debit_amount;
                                    if ($tlc_wallet_info->payment_mode == 1) {
                                        $payment_mode = 'Cash';//assign if payment mode is 1
                                    }elseif ($tlc_wallet_info->payment_mode == 2) {
                                        $payment_mode = 'Card';//assign if payment mode is 2
                                    }elseif ($tlc_wallet_info->payment_mode == 3) {
                                        $payment_mode = 'PayTM';//assign if payment mode is 3
                                    }else{
                                        $payment_mode = null;//assign if payment mode is null
                                    }
                                    if (!empty($tlc_wallet_info->debit_amount)) {
                                        $last_transaction_amount = $tlc_wallet_info->debit_amount.' (debited)';    
                                    } else { 
                                        $last_transaction_amount = $tlc_wallet_info->credit_amount.' (credited)';
                                    }   
                                    
                                    $last_transaction_date = 
                                    Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tlc_wallet_info->created_at, 'UTC')
                                       ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');    

                                   /* date('d M, Y H:i a', strtotime());*/
                                    /*$transaction_pin = $tlc_wallet_info->transaction_id;*/
                                    $transaction_date= Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tlc_wallet_info->created_at, 'UTC')
                                       ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i A');
                                    ?>
                                @else
                                <?php
                                //assign all null is variable tlc_wallet_info is not set or null
                                     $current_balance = 'Not Found';
                                     $credit_amount = 'Not Found';
                                     $payment_mode = 'Not Found';
                                     $last_transaction_amount ='Not Found';
                                    $last_transaction_date ='Not Found';
                                    /*$transaction_pin ='Not Found';*/
                                    $transaction_date='Not Found';

                                ?>
                                @endif
                                 <div class="col-md-6">
                                    <div class="col-md-8">
                                        {{ Form::label('current_balance_id',Lang::get('views.current_balance'), ['class' => "control-label"]) }}
                                    </div>
                                    <div class="col-md-4">
                                      @if(is_numeric($current_balance))
                                        <b> &#x20B9; </b> 
                                       @endif 
                                       <label class="control-label">{{$current_balance == null ? 0 : $current_balance}}</label> 
                                       <!-- {{ Form::label('current_balance',$current_balance == null ? 'Not Found' : $current_balance, ['class' => "control-label"]) }}  -->
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                        {{ Form::label('last_transaction_date_id', Lang::get('views.last_transaction_date') , ['class' => "control-label"]) }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::label('last_transaction_date',$last_transaction_date == null ? 'Not Found' : $transaction_date, ['class' => "control-label"]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-12">
                                    <div class="col-md-4">
                                        {{ Form::label('last_transaction_amount_id', Lang::get('views.last_transaction_amount') , ['class' => "control-label"]) }}
                                    </div> 
                                    <div class="col-md-6 paddingleft5">
                                       @if($last_transaction_amount)
                                        <b> &#x20B9; </b> 
                                       @endif
                                       {{Form::label('last_transaction_amount',$last_transaction_amount == null ? 'Not Found' : $last_transaction_amount , ['class' => "control-label"]) }}
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                             @if(!empty($tlc_wallet_info->comment))
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        {{ Form::label('comment', Lang::get('views.last_comment') , ['class' => "control-label"]) }}
                                    </div> 
                                    <div class="col-md-8 paddingleft5">
                                       {{ Form::label('comment',$tlc_wallet_info->comment == null ? 'Not Found' : $tlc_wallet_info->comment , ['class' => "control-label"]) }}
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/customer', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                            </div>
                        </div>
                        </div>
                    </div>
            </div>
            <!-- model popup for recharge button -->
            <div id="recharge" class="modal">
                    
                          <div class="modal-content">
                            <span onclick="fun_popup_close('recharge');" class="close">&times;</span>
                        <div id="kra" class="tab-pane in active scroll-data">
                        </div>
                <div class="row">
                    {{ Form::open(['url' => 'customer/recharge', 'method' => 'post', 'id' => 'create-recharge','files'=>true]) }}
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('debit_amount') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('Dembit Amount', Lang::get('views.debit_amount'), ['class' => "control-label"]) }}
                                {{ Form::text('debit_amount', null,['class' => 'form-control','placeholder'=>Lang::get('views.debit_amount'),'required']) }}
                                @if ($errors->has('debit_amount'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('debit_amount') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('payment_mode') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('payment_mode_id', Lang::get('views.payment_mode'), ['class' => "control-label"]) }}

                                {{ Form::select('payment_mode',$payment_modes,null,['class' => 'form-control','placeholder'=>Lang::get('views.payment_mode'),'required']) }}
                                @if ($errors->has('payment_mode'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('payment_mode') }}</strong>
                                </span>
                                @endif
                            </div>
                             <div class="form-group">
                            <div class="pull-right">
                                {{ Form::submit(Lang::get('views.recharge'), ['class' => 'btn btn-primary','onClick'=>'fun_popup("recharge")']) }}
                            </div>
                        </div>
                        {{ Form::Close() }}
                    </div>
                </div>
                </div>
                <!-- end popup model recharge -->

                <!-- model popup for issue band button -->
            <div id="issue_band" class="modal">
                    
                          <div class="modal-content">
                            <span onclick="fun_popup_close('issue_band');" class="close">&times;</span>
                        <div id="kra" class="tab-pane in active scroll-data">
                        </div>
               
                        <div class="row">
                                {{ Form::open(['url' => 'customer/issue_band', 'method' => 'post', 'id' => 'create-uuid','files'=>true]) }}
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('unique_id') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('Dembit Amount', Lang::get('views.unique_id'), ['class' => "control-label"]) }}
                                {{ Form::text('unique_id', null,['class' => 'form-control','placeholder'=>Lang::get('views.unique_id'),'required']) }}
                                @if ($errors->has('unique_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('unique_id') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('UUID') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('uuid', Lang::get('views.UUID'), ['class' => "control-label"]) }}

                                {{ Form::text('UUID',null,['class' => 'form-control','placeholder'=>Lang::get('views.UUID'),'required']) }}
                                @if ($errors->has('UUID'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('UUID') }}</strong>
                                </span>
                                @endif
                            </div>
                             <div class="form-group">
                            <div class="pull-right">
                                {{ Form::submit(Lang::get('views.issue_band_btn'), ['class' => 'btn btn-primary','onClick'=>'fun_popup("issue_band")']) }}
                            </div>
                        </div>
                        {{ Form::Close() }}
                               
                            </div>
                
                </div>
                </div>
                <!-- end popup model issue band -->


        </div> <!-- End class container-fluid -->
    </div> <!-- End class panel panel-default -->
            </div> <!-- End class col-md-8 col-md-offset-2 -->
        </div> <!-- End class row -->
    </div> <!-- End class container -->
@endsection
@section('scripts')
<script type="text/javascript">
    $('#myTabs a').click(function (e) {
    e.preventDefault();
  
    var url = $(this).attr("data-url");
    var href = this.hash;
    var pane = $(this);
    
    // ajax load from data-url
    $(href).load(url,function(result){      
        pane.tab('show');
    });
});

// load first tab content
$('#home').load($('.active a').attr("data-url"),function(result){
  $('.active a').tab('show');
});

</script>

 <script type="text/javascript">
   // Get the modal
function fun_popup(id){
  var modal = document.getElementById(id);
  modal.style.display = "block";
}
function fun_popup_close(id){
  var modal = document.getElementById(id);
  modal.style.display = "none";

}
</script>

<script type="text/javascript">
    $(document).ready(function () {
  $("#create-recharge").validate({
    debug: false,
    rules: {
      type: {
        required: true
      },
      messages: {
        //messages for required
      }
    }
  });
}); 
</script>
<script type="text/javascript">
 $(document).ready(function(){
   $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
       localStorage.setItem('activeTab', $(e.target).attr('href'));
   });
   var activeTab = localStorage.getItem('activeTab');
   if(activeTab){
       $('#myTabs a[href="' + activeTab + '"]').tab('show');
   }
});
</script>
    {!! JsValidator::formRequest('App\Http\Requests\CategoryRequest', '#create-category') !!}
@endsection

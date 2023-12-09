<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Name</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
     <link rel="stylesheet" href="{{ asset('css/style8.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style77.css') }}">
</head>
<body>   
		<header>
			<div class="header-row">
				<div class="header-item">
				 <a href="<?php echo '/invoice' ?>"> <i class="fa-solid fa-arrow-left"></i> </a>	
					<span> Create Invoice </span>
					<a href="<?php echo '/main' ?>">   <img src="/image/logo-phone.png" alt="Logo"> </a>   
				</div>
			</div>
		</header>	

    @include('layouts.partials.footer')  

    <div class="container" >

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
{{-- {{$jobs}} --}}
    @if(session('go_back'))
            <script>
               setTimeout(function() {
            window.history.go(-2); 
            }, 1000); 
            </script>
        @endif
        
        <form action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
             @csrf 
            <fieldset class="m-3">
               <div class="row mb-3 mt-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"> To : </label>
                    </div>
                    <div class="col-7">
                        <input type="hidden" name="selected_type" id="selectedType" value="">
                        <select name="customer_id" class="custom-input" id="customerSelect">
                            <option value="" disabled selected>Select a Customer</option>
                             {{-- <optgroup label="Buliders" disabled> </optgroup> --}}
                            @foreach($admin_buliders as $admin_bulider)
                                <option value="{{ $admin_bulider->company_name}}"  data-email="{{ $admin_bulider->builder_email }}">{{ $admin_bulider->company_name }}</option>
                            @endforeach
                             {{-- <optgroup label="Customers" disabled> </optgroup> --}}
                            @foreach($customers as $customer)
                                <option value="{{ $customer->name }}" data-email="{{ $customer->email }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-3 d-flex align-items-center justify-content-center">
                        <a href="<?php echo '/customer/create' ?>" class="form-label">
                            <i class="fas fa-user-plus"></i> ADD
                        </a>
                    </div>
                    
                </div>

               <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"><i class="fa-solid fa-envelope"></i> </label>
                    </div>
                     <div class="col-10">
                   
                      <input type="email" class="custom-input editable" id="customer_email" value="" name="send_email" placeholder="Enter Email" required>
                      <span id="email-error" class="error-message text-danger"></span>
                    </div>  
                </div>
                <hr>
                 <!-- Invoice Number -->
                 <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"><i class="fas fa-file-invoice"> </i> </label>
                    </div>
                    <div class="col-10">
                        <input type="text" class="custom-input" value="INV: {{ isset($inv_numbers) ? str_pad($inv_numbers + 1, 5, '0', STR_PAD_LEFT) : 'Default Value' }}" id="invoiceNumber" name="inv_number" readonly> 
                    </div>
                </div> 

                <!-- Date -->
                <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label text-center">
                            <i class="fas fa-calendar-alt"></i>
                        </label>
                    </div>
                    
                    <div class="col-10">
                        <input type="date" class="custom-input editable" id="dateInput" name="date">
                    </div>
                </div>               
        
                <!-- Due Date -->
                <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"><i class="far fa-clock"></i></label>
                    </div>
                    <div class="col-10">
                        <input type="text" class="custom-input editable" placeholder="Purchase Order Number" name="purchase_order">
                    </div>
                </div>
        
     
                <!-- Address -->
                <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"><i class="fas fa-map-marker-alt"></i></label>
                    </div>   
                    <div class="col-10">                        
                                 <textarea class="custom-input editable" id="searchTextField" name="address" id="address" rows="1" placeholder="Job Address" name="job_selection"></textarea>
                                 <input type="hidden" name="latitude" id="Lat" value="">
                                 <input type="hidden" name="longitude" id="Lng" value="">                            
              
                    </div>           
                </div>
                        
               
        
                <!-- Description -->
                <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label"><i class="fa-regular fa-bookmark"></i></label>
                    </div>
                 <div class="col-10">
                        <input type="text" class="custom-input editable" placeholder="Short description of work " name="description" >
                    </div>
                </div>   

                <!-- Attachment Field -->
                <div class="row mb-3">
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <label class="form-label text-center">
                            <i class="fas fa-paperclip"></i>
                        </label>
                    </div>
                        <div class="col-10">
                        <input type="file" class="form-control" id="attachmentInput" name="attachment">
                        @error('attachment')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
        
                <hr>

                 <!-- Total Due -->
                 <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Job Details:</label>
                    </div>
                    <div class="col-12">
                        <input type="text" class="custom-input editable" class="form-control" placeholder="Extra description (Optional)" name="job_details" >
                    </div>
                </div>
        <hr/>
       <input type="hidden" value="{{ $job_number ?? '' }}" name="job_id">


{{-- 
            <input type="text" value="@if($jobs) {{$jobs->id}} @endif" name="job_id"> --}}
             <div class="row mb-3">
            <!-- Amount Input -->
            <div class="col-6">
                <label class="form-label">Amount:</label>
            </div>
            <div class="col-6">
                <div class="input-group">
                    <span class="input-group-text no-background"><i class="fas fa-dollar-sign"></i></span>
                    <input type="text" id="amountInput" class="custom-input form-control text-right editable" name="amount" placeholder="Enter Amount">
                </div>
            </div>

            <!-- GST Input -->
            <div class="col-6">
                <label class="form-label">GST :</label>
            </div>
            <div class="col-6">
                <div class="input-group">
                    <span class="input-group-text no-background"><i class="fas fa-dollar-sign"></i></span>
                    <input type="text" id="gstInput" class="custom-input form-control text-right editable" value="0.00" name="gst" readonly>
                </div>
            </div>
        </div>

        <hr/>


                  

        <!-- Total Due Input -->
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Total Due:</label>
            </div>
            <div class="col-6">
                <div class="input-group">
                    <span class="input-group-text no-background"><i class="fas fa-dollar-sign"></i></span>
                    <input type="text" id="totalDueInput" class="custom-input form-control text-right editable" value="0.00" name="total_due" readonly>
                </div>
            </div>
        </div>  
                
        <!-- Status filde -->

         <input type="text" name="status" value="1" class="form-control @error('status') is-invalid @enderror" hidden>           

                <div class="row mt-3">
                    <div class="col-5">
                        <button type="submit" name="action" class="btn btn-primary btn-block btnshow" value="save">Save</button>
                    </div>
                    <div class="col-2">
                       
                    </div>
                    <div class="col-5">
                        <button type="submit" name="action" class="btn btn-success btn-block btnshow" value="send&save">Send</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

  
    <div style="margin: 20px 0px 300px 0px;"></div>
   
     
       
    
</body>

<script type="text/javascript">
    $('.brand').on('change', function(e) {
        var val = $(this).val();
        $('.brand-cst').val(val)
    });
    $('#builder_company').on('change', function(e) {

        var val = $(this).attr('data-brand');;
        var selectedOption = $(this).find('option:selected');
        var dataBrandValue = selectedOption.data('brand');
        $('#brand_id').val(dataBrandValue).change();
    });


    function initialize() {
        var input = document.getElementById('searchTextField');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            document.getElementById('Lat').value = place.geometry.location.lat();
            document.getElementById('Lng').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    $('#supervisor option').hide();
    var selectedOptionValue = $('#builder_company').val();
    if (selectedOptionValue != '') {
        $('.empty_supervisor').show()
        $('.supervisor_' + selectedOptionValue).show()
    }
    $('#builder_company').change(function() {
        builder_id = this.value;
        if (builder_id === '') {
            $('#supervisor').val(builder_id);
            $('#supervisor option').hide();
        } else {
            $('#supervisor option').hide();
            $('.empty_supervisor').show().prop('selected', true);
            $('.supervisor_' + builder_id).show();
        }
    })
</script>
<script src="{{ asset('js/script.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCb7MpXPNGT9y6LKzg_bi8R1Q_hwmLKMgk&libraries=places&callback=initialize" async defer></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>


<script>


  const emailInput = document.getElementById('customer_email');
  const emailError = document.getElementById('email-error');

  emailInput.addEventListener('input', function() {
    const email = emailInput.value;
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    if (!emailPattern.test(email)) {
      emailError.textContent = 'Invalid email format';
    } else {
      emailError.textContent = '';
    }
  });


document.addEventListener("DOMContentLoaded", function () {
    const customerSelect = document.getElementById('customerSelect');
    const customerEmailInput = document.getElementById('customer_email');

    customerSelect.addEventListener('input', function () {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const selectedEmail = selectedOption.getAttribute('data-email');
        customerEmailInput.value = selectedEmail;
    });
});

// //for invoice Random Number
//     var invoiceNumberInput = document.getElementById("invoiceNumber");
//     var randomNumber = Math.floor(Math.random() * 1000000) + 1;
//     invoiceNumberInput.value = "INV- "+ randomNumber;

//Defult todays date..

 var dateInput = document.getElementById("dateInput");

    // Create a new Date object to get the current date
    var currentDate = new Date();

    // Format the current date as YYYY-MM-DD
    var year = currentDate.getFullYear();
    var month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
    var day = currentDate.getDate().toString().padStart(2, "0");
    var formattedDate = year + "-" + month + "-" + day;
    dateInput.value = formattedDate;

//celect the types

var customerSelect = document.getElementById("customerSelect");
var selectedTypeInput = document.getElementById("selectedType");

customerSelect.addEventListener("change", function() {
    var selectedOption = this.options[this.selectedIndex];
    var optionType = selectedOption.getAttribute("data-type");

    // Set the selected type in the hidden input field
    selectedTypeInput.value = optionType;
});


</script>



</html>
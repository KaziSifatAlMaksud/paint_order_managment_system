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
        <style>
    /* CSS for fixed position */
    .fixed-top {
        position: fixed;
        top: 0;
        margin-top: 75px; 
        width: 100%;
        z-index: 999;
    }
</style>
 
    	<header>
			<div class="header-row">
				<div class="header-item">
				 <a href="<?php echo '/invoice' ?>"> <i class="fa-solid fa-arrow-left"></i> </a>	
					<span> Late Invoices </span>
					<a href="<?php echo '/main' ?>">   <img src="/image/logo-phone.png" alt="Logo"> </a>   
				</div>
			</div>
		</header>
 


@include('layouts.partials.footer')  
        
      

    <div class="container" style="padding-top: 30px">
          
    
        <div class="search-bar" >
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" id="search-invoice" placeholder="Search Here" oninput="filterCards2()">
        </div>
   
    
        <div class="portfolio-container">
            <!-- Card Content -->
            
            
             @foreach($invoices as $invoice)
                 {{-- @if(!$invoice->job_id && $invoice->status == 1 )
               <div  class="card InvoicePortfolio invoice-item  filter-ready">
                <a href="{{ '/manual_invoice/' . $invoice->id }}">
                <div  class="cardhaderInvoice-link">
                    <h5 class="text-left ml-2 mt-1 showinline" style="width: 70%" id="expandable-title">{{ $invoice->address}}</h5>
                    <button type="button" class="invoiceReadynotification-button">Ready</button>
                </div>
                        <p class="text-center mt-2">This invoice is ready to send to cuestomer</p>
      
                        <div class="row p-1">
                            <div class="col-6 reduced-line-height">
                                <p class="text-left showinline"> {{$invoice->customer_id}}</p>
                                <p class="text-left showinline"> {{$invoice->description}} </p>
                            </div>
                            <div class="col-6 reduced-line-height">
                                <b>
                                <?php
                                $invoiceDate = new DateTime($invoice->date);
                                $currentDate = new DateTime('now');
                                $interval = $invoiceDate->diff($currentDate);                           
                                $days = $interval->days;                     
                                if ($days <= 5) {
                                    echo "5 days or less";
                                } elseif ($days <= 7) {
                                    echo "1 week";
                                } elseif ($days <= 14) {
                                    echo "2 weeks";
                                } elseif ($days <= 21) {
                                    echo "3 weeks";
                                } elseif ($days > 21) {
                                    echo "1 month or more";
                                }                         
                                ?>
                                </b>
                                <p class="text-right"><b>Start:</b> <br>  <?php echo (new DateTime($invoice->date))->format('d-m-Y'); ?> </p>
                                <p class="text-right">{{$invoice->inv_number}} </p>
                                <p class="text-right font-weight-bold">${{$invoice->total_due}}</p>
                            </div>
                        </div>
                
                    </a>    
                </div>
                @endif --}}

                @if(!$invoice->job_id && $invoice->status == 2)   
                 <div class="card InvoicePortfolio invoice-item  filter-unpaid">
                    <a href="{{ '/manual_invoice/' . $invoice->id }}">
                <div  class="cardhaderInvoice-unpaid">
                 <h5 class="text-left ml-2 mt-1 showinline" style="width: 70%" id="expandable-title">{{ $invoice->address}}</h5>
                 <button type="button" class="invoiceReadynotification-unpaid">Unpaid</button>
                </div>
   
                <p class="text-center mt-2">waiting or payment, i paid click receaved</p>
                           
                     <div class="row p-1">

                        <h6 class=" col-12 text-left">  <b>SENT: </b><?php echo (new DateTime($invoice->updated_at))->format('d-m-Y'); ?> </h6>
                         <div class="col-6 reduced-line-height">
                             <p class="text-left showinline">{{$invoice->customer_id}}</p>
                             <p class="text-left showinline">{{$invoice->description}} </p>
                         </div>
                         <div class="col-6 reduced-line-height">
                            <b>
                               <?php
                                $invoiceDate = new DateTime($invoice->date);
                                $currentDate = new DateTime('now');
                               
                                // Calculate the difference
                                $interval = $invoiceDate->diff($currentDate);

                                // Get the difference in total days
                                $days = $interval->days;

                                // Determine the time stamp
                                if ($days <= 5) {
                                    echo "5 days or less";
                                } elseif ($days <= 7) {
                                    echo "1 week";
                                } elseif ($days <= 14) {
                                    echo "2 weeks";
                                } elseif ($days <= 21) {
                                    echo "3 weeks";
                                } elseif ($days > 21) {
                                    echo "1 month or more";
                                }
                         
                                ?>

                                       </b>
                             <p class="text-right"><b>Start:</b> <br> 
                                
                                <?php echo (new DateTime($invoice->date))->format('d-m-Y'); ?> </p>
                             <p class="text-right"> {{$invoice->inv_number}}</p>
                             <p class="text-right font-weight-bold">${{$invoice->total_due}}</p>
                         </div>
                     </div>
                 </a>   
             </div>   
              {{-- @elseif(!$invoice->job_id && $invoice->status == 3) 
                <div class="card InvoicePortfolio invoice-item  filter-paid">
                   <a href="{{ '/manual_invoice/' . $invoice->id }}"> 
                <div  class="cardhaderInvoice-paid">
                 <h5 class="text-left ml-2 mt-1 showinline" style="width: 70%" id="expandable-title">{{ $invoice->address}}</h5>
                 <button type="button" class="invoiceReadynotification-paid">Paid</button>
                </div>
 
                <p class="text-center mt-2"><b>PAID ON : </b><?php echo (new DateTime($invoice->updated_at))->format('d-m-Y'); ?></p>
                           
                     <div class="row p-1">
                        <h6 class=" col-12 text-left"> <b>PAID ON : </b><?php echo (new DateTime($invoice->updated_at))->format('d-m-Y'); ?> </h6>
                         <div class="col-6 reduced-line-height">
                           
                             <p class="text-left showinline">{{$invoice->customer_id}}</p>
                             <p class="text-left showinline"> {{$invoice->description}} </p>
                         </div>
                         <div class="col-6 reduced-line-height">
                             <p class="text-right">  {{$invoice->inv_number}}</p>
                             <p class="text-right font-weight-bold">${{$invoice->total_due}}</p>
                         </div>
                     </div>
                      </a>  
               </div>      --}}
                @endif         
            @endforeach 

          

           
            <!-- End Card Section -->   
        </div>
        <div style="margin: 20px 0px 300px 0px;"></div>
    </div>
</body>

    <script>
    window.addEventListener('scroll', function() {
        var headerHeight = document.querySelector('.header-row').offsetHeight;
        var filterElement = document.querySelector('.filter');
        var searchBarElement = document.querySelector('.search-bar');
        if (window.pageYOffset > headerHeight) {           
            filterElement.classList.add('fixed-top');
            searchBarElement.classList.add('fixed-top');
        } else {
            filterElement.classList.remove('fixed-top');
            searchBarElement.classList.remove('fixed-top');
        }
    });
</script>


    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Name</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style77.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style8.css') }}">
</head>
<body> 
  



		<header>
			<div class="header-row">
				<div class="header-item">
				 <a href="<?php echo '/main' ?>"> <i class="fa-solid fa-arrow-left"></i> </a>	
					<span> Company Name </span>
					<a href="<?php echo '/main' ?>">   <img src="/image/logo-phone.png" alt="Logo"> </a>   
				</div>
			</div>
		</header>	

        @include('layouts.partials.footer')  
    
  

  


<div class="container" >
  	<!-- docs_area start -->
      <section class="docs_area jobs_area">            

      <a href=" <?php echo'/new_order' ?>">
        <div class="docs_part docs_prt1 jobs_part2">
            <div class="docs_left jobs_cnt">
                <h4>Garage Paint</h4>
                <p>Make a list of all the colours & product you have at home, be notified when ordering that you already have this colour.</p>
            </div>
            <div class="docs_right">
                <img src="/image/paint-1.png" alt=""  >
            </div>
        </div>
    </a>

       


        
    </section>
    <!-- docs_area end -->
            {{-- <!-- <div class="filepop"> <img src="img/filepage/add.png" alt="" width="40" height="40"></div> --> --}}       
            <div style="margin: 20px 0px 300px 0px;"></div>
</div>
</body>

<script src="{{ asset('js/script.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


</html>




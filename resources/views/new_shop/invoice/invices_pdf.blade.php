<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> | Shop </title>
    <style>
        body{
            background-color: #F6F6F6; 
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            padding: 0;
        }
        p{
            margin: 0;
            padding: 0;
        }
        .brand-section{
            margin-top: 50px;
            line-height: 1.6;
            padding-bottom: 20px
        }
        .container{
            width: 80%;
            margin-right: auto;
            margin-left: auto;
        }
        
        .logo{
            width: 50%;
        }

        .row{
            display: flex;
            flex-wrap: wrap;
        }
        .col-6{
            width: 50%;
            flex: 0 0 auto;
        }
        .col-4{
            width: 20%;
            flex: 0 0 auto;
        }
  
        .body-section{
            /* padding: 16px; */
            /* border: 1px solid gray; */
        }
        .heading{
            font-size: 20px;
            margin-bottom: 08px;
        }
        .sub-heading{
            color: #262626;
            margin-bottom: 05px;
        }
     table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
      
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .text-right{
            text-align: end;
        }
        .w-20{
            width: 20%;
        }
        .float-right{
            float: right;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="brand-section">
             <h1 style="color: #5086e6">  Final Coat</h1>
            <div class="row">
                <div class="col-6">                   
                        <p >56 Myall st Concord</p>
                        <p >ABN 83094324633</p>
                        <p>Gus: 0405701954</p>                    
                </div>
                 <div class="col-6" >                    
                        <h4 style=" text-align:end; ">Invoice</h4>
                </div>
            </div>
        </div>


            <div class="row">
                <div class="col-6">                                    
                </div>
                 <div class="col-6">                    
                        <p><b>INVOICE # </b> {{$inv_number}} </p>
                         <P><b>Type Of work done:</b>{{$description}}                       
                            </P>
                </div>
            </div>
        

    

         <div class="body-section">
            <div class="row">
                <div class="col-8">
                  <p class="sub-heading"><b>Send Date:</b> {{ date('d F, Y', strtotime($date)) }}</p>
                    <p class="sub-heading"><b> Bill To:</b> {{$customer_id}} </p>
                </div>
                <div class="col-4">
                    
                </div>
            </div>
        </div>
        <hr>
        <div class="body-section">
            
            <p> <b>Job Address: </b> 
                {{$address}}.
            </p>
            <br>
            <table class="table-bordered">
                <thead>
                    <tr style="background-color: #5086e6; color:#fff;">
                        <th>Product</th>
                        <th class="w-20">Price</th>
        
                    </tr>
                </thead>
                <tbody>
                      <tr>
                        <td><b>Purchase Order:</b></td>
                        <td></td>               
                    </tr>
                    <tr>
                        <td> {{$job_details}} </td>
                        <td class="text-right" >$
                            {{$amount}} 
                        </td>
                    </tr>
                     <tr>
                        <td style="padding: 20px" ></td>
                        <td style="padding: 20px" ></td>
                    </tr>
                    <tr>
                        <td style="padding: 20px"></td>
                        <td style="padding: 20px" ></td>
                    </tr>

                      <tr>
                        <td class="text-right">Amount:</td>
                        <td class="text-right" >$
                             {{$amount}} 
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="text-right">Gst:</td>
                        <td class="text-right" >$
                             {{$gst}}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right"><b>Total Due:</b></td>
                        <td class="text-right"><b> $
                             {{$total_due}} 
                        </b></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <br>           
        </div>           
    </div>
</body>
</html>
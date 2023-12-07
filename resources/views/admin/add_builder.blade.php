<?php
require  public_path() . '/admin/header.blade.php';
?>
<div class="page-header">
    <h1 class="page-title">
        Add New Builder
    </h1>
    <br />

    <div class="row">
        <div class="col-sm-9">

            <div class="row">
                <form method="post" id="add_builder" action="{{route('admins.admin_builder.store')}}">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Company Name </label>
                        @if ($errors->has('company_name'))
                        <p class="error">{{ $errors->first('company_name') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="company_name" placeholder="Company Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Builder Email: </label>
                        @if ($errors->has('company_email'))
                        <p class="error">{{ $errors->first('company_email') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="builder_email" placeholder="Builder email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Painter/Builder Account: </label>
                        @if ($errors->has('account_type'))
                        <p class="error">{{ $errors->first('account_type') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <select class="form-control" name="brand_id" required>
                                @foreach ($brands as $brand)
                                
                                 <option value={{$brand->id}}>{{$brand->name }}</option>
                                @endforeach
                              
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Builder Phone Number: </label>
                        @if ($errors->has('phone_number'))
                        <p class="error">{{ $errors->first('phone_number') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="phone_number" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Builder Address: </label>
                        @if ($errors->has('address'))
                        <p class="error">{{ $errors->first('address') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="address" placeholder="Address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Abn: </label>
                        @if ($errors->has('abn'))
                        <p class="error">{{ $errors->first('abn') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="abn" placeholder="Abn" required>
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-sm-3 control-label">Gate Code: </label>
                        @if ($errors->has('abn'))
                        <p class="error">{{ $errors->first('abn') }}</p>
                        @endif
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="gate" placeholder="Gate Code" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3 text-center "><br>
                            <button type="submit" class=" btn btn-primary">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require  public_path() . '/admin/footer.blade.php';
?>
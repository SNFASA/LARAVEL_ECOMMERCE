@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Brand</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('brands.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <form action="{{ route('brands.update', $brand->id) }}" method="POST" name="BrandForm" id="BrandForm">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$brand->name}}">
                                <p class="invalid-feedback"></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="{{$brand->slug}}">
                                <p class="invalid-feedback"></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{($brand->status ==1 )? 'selected' :''}}value="1">Active</option>
                                    <option  {{($brand->status ==0 )? 'selected' :''}}value="0">Block</option>
                                </select>
                            </div>
                        </div>									
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button typ="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('brands.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection

@section('customJS')
<script>
    $("#BrandForm").submit(function(event) {
    event.preventDefault();
    var element = $("#BrandForm");
    $("button[type=submit]").prop('disabled', true);
    $.ajax({
        url: '{{ route("brands.update", $brand->id) }}',  // Updated to use the correct route
        type: 'POST',  // Use POST here since we're using @method('PUT') in the form
        data: element.serializeArray(),
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $("button[type=submit]").prop('disabled', false);
            if (response.status === true) {
                window.location.href = "{{ route('brands.index') }}";
            } else {
                var errors = response.errors;
                if (errors.name) {
                    $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                } else {
                    $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                }
                if (errors.slug) {
                    $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.slug);
                } else {
                    $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                }
            }
        },
        error: function(jqXHR, exception) {
            console.log("Something went wrong", jqXHR.responseText);
        }
    });
});

</script>
@endsection

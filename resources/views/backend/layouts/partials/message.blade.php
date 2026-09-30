@if ($errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <ul>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </ul>

    </div>
@endif



@if(Session::has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
     <h5>{{ Session('success') }}</h5>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
   <h5>{{ Session('error') }}</h5>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
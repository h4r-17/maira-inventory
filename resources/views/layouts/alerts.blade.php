@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-close-alert" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show auto-close-alert" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Data belum valid!</strong>
        <ul class="mb-0 mt-2 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@push('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $(".auto-close-alert").fadeTo(200, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 2000);
        });
    </script>
@endpush

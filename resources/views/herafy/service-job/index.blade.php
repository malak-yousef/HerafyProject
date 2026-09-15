@extends('herafy.parent')

@section('title', 'أعمالي')

@section('content')
<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <h2 class="fw-bold mb-4"><i class="fas fa-briefcase text-warning"></i> أعمالي المتفق عليها</h2>

        <div class="row g-4">
            @forelse ($jobs as $job)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3 mb-3">
                                <h5 class="fw-bold">{{ $job->order->title ?? '-' }}</h5>
                                <span class="badge bg-warning text-dark">{{ $job->status }}</span>
                            </div>
                            <p><strong>السعر المتفق عليه:</strong> {{ $job->offer->price ?? '-' }}</p>
                            <a href="{{ route('herafy.craftman.jobs.show', $job) }}" class="btn btn-outline-warning w-100">عرض تفاصيل العمل</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-info text-center">لا توجد أعمال متفق عليها حتى الآن.</div></div>
            @endforelse
        </div>

        <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
</section>
@endsection

@extends('cms.parent')

@section('title', 'تعديل البلاغ')

@section('content')

<div class="container">

    <h2 class="mb-4">
        تعديل البلاغ #{{ $report->id }}
    </h2>

    <form action="{{ route('reports.update', $report->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label class="form-label">
                المبلّغ
            </label>

            <select name="reporter_id" class="form-control">

                @foreach($users as $user)

                    <option value="{{ $user->id }}"
                        {{ $report->reporter_id == $user->id ? 'selected' : '' }}>

                        {{ $user->name }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">
                المبلّغ عليه
            </label>

            <select name="reported_id" class="form-control">

                @foreach($users as $user)

                    <option value="{{ $user->id }}"
                        {{ $report->reported_id == $user->id ? 'selected' : '' }}>

                        {{ $user->name }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Job
            </label>

            <select name="job_id" class="form-control">

                @foreach($jobs as $job)

                    <option value="{{ $job->id }}"
                        {{ $report->job_id == $job->id ? 'selected' : '' }}>

                        Job #{{ $job->id }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">
                سبب البلاغ
            </label>

            <input type="text"
                   name="reason"
                   class="form-control"
                   value="{{ old('reason', $report->reason) }}">

        </div>


        <div class="mb-3">

            <label class="form-label">
                الوصف
            </label>

            <textarea name="description"
                      class="form-control"
                      rows="5">{{ old('description', $report->description) }}</textarea>

        </div>


        <div class="mb-3">

            <label class="form-label">
                الحالة
            </label>

            <select name="status" class="form-control">

                <option value="pending"
                    {{ $report->status === 'pending' ? 'selected' : '' }}>
                    قيد الانتظار
                </option>

                <option value="reviewing"
                    {{ $report->status === 'reviewing' ? 'selected' : '' }}>
                    قيد المراجعة
                </option>

                <option value="resolved"
                    {{ $report->status === 'resolved' ? 'selected' : '' }}>
                    تم الحل
                </option>

                <option value="rejected"
                    {{ $report->status === 'rejected' ? 'selected' : '' }}>
                    مرفوض
                </option>

            </select>

        </div>


        <button type="submit" class="btn btn-primary">
            تحديث
        </button>

        <a href="{{ route('reports.index') }}"
           class="btn btn-secondary">
            إلغاء
        </a>

    </form>

</div>

@endsection
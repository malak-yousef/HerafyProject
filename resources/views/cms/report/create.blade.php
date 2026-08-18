@extends('cms.parent')

@section('title', 'إضافة بلاغ')

@section('content')

<div class="container">

    <h2 class="mb-4">إضافة بلاغ</h2>

    <form action="{{ route('reports.store') }}" method="POST">

        @csrf

        <div class="mb-3">

            <label class="form-label">
                المبلّغ
            </label>

            <select name="reporter_id" class="form-control">

                <option value="">اختر المستخدم</option>

                @foreach($users as $user)

                    <option value="{{ $user->id }}"
                        {{ old('reporter_id') == $user->id ? 'selected' : '' }}>

                        {{ $user->name }}

                    </option>

                @endforeach

            </select>

            @error('reporter_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>


        <div class="mb-3">

            <label class="form-label">
                المبلّغ عليه
            </label>

            <select name="reported_id" class="form-control">

                <option value="">اختر المستخدم</option>

                @foreach($users as $user)

                    <option value="{{ $user->id }}"
                        {{ old('reported_id') == $user->id ? 'selected' : '' }}>

                        {{ $user->name }}

                    </option>

                @endforeach

            </select>

            @error('reported_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>


        <div class="mb-3">

            <label class="form-label">
                Job
            </label>

            <select name="job_id" class="form-control">

                <option value="">اختر الـ Job</option>

                @foreach($jobs as $job)

                    <option value="{{ $job->id }}"
                        {{ old('job_id') == $job->id ? 'selected' : '' }}>

                        Job #{{ $job->id }}

                    </option>

                @endforeach

            </select>

            @error('job_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>


        <div class="mb-3">

            <label class="form-label">
                سبب البلاغ
            </label>

            <input type="text"
                   name="reason"
                   class="form-control"
                   value="{{ old('reason') }}">

            @error('reason')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>


        <div class="mb-3">

            <label class="form-label">
                الوصف
            </label>

            <textarea name="description"
                      class="form-control"
                      rows="5">{{ old('description') }}</textarea>

        </div>


        <div class="mb-3">

            <label class="form-label">
                الحالة
            </label>

            <select name="status" class="form-control">

                <option value="pending">قيد الانتظار</option>
                <option value="reviewing">قيد المراجعة</option>
                <option value="resolved">تم الحل</option>
                <option value="rejected">مرفوض</option>

            </select>

        </div>


        <button type="submit" class="btn btn-primary">
            حفظ
        </button>

        <a href="{{ route('reports.index') }}"
           class="btn btn-secondary">
            رجوع
        </a>

    </form>

</div>

@endsection
@extends('cms.parent')

@section('title', 'الحرفيون')

@section('styles' )

@endsection

@section('main_content' , 'الحرفيون')

@section('content')
<div id="section-craftsmen" class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">🔧 إدارة الحرفيين</h4>
            <p class="text-muted small">عرض وإدارة جميع الحرفيين المسجلين</p>
        </div>
        <div>
            <button class="btn btn-success btn-sm" onclick="addCraftsman()">
                <i class="fas fa-plus"></i> إضافة حرفي
            </button>
            <button class="btn btn-warning btn-sm" onclick="exportData('craftsmen')">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <input type="text" class="form-control form-control-sm" id="searchCraftsmen" placeholder="🔍 بحث عن حرفي..."
                oninput="filterTable('craftsmen')" style="width: 300px;">
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الحرفة</th>
                        <th>الخبرة</th>
                        <th>التقييم</th>
                        <th>عدد التقييمات</th>
                        <th>تاريخ الانضمام</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="craftsmenTableBody">
                    @foreach($craftmen as $craftman)
                    <tr>
                        <td>{{ $craftman->id }}</td>
                        <td>{{ $craftman->email}}</td>
                        <td>{{ $craftman->craft_name}}</td>
                        <td>{{ $craftman->experience_years}}</td>
                        <td>{{ $craftman->rating}}</td>
                        <td>{{ $craftman->total_reviews}}</td>
                        <td>{{ $craftman->timestamps}}</td>
                        <td>{{ $craftman->is_active }}</td>
                        <td>actions</td>
                        <!-- <td colspan="9" class="text-center text-muted">لا يوجد حرفيين</td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!--card body -->
        <div class="card-footer clearfix">
            {{$craftmen->links()}}
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection
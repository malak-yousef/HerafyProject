@extends('cms.parent') @section('content')
<div class="container-fluid px-4 pt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">
                        <i class="feather-shield me-2"></i> 
                        Permissions Matrix for Role: <span class="badge bg-warning text-dark fs-6">{{ $role->name }}</span>
                    </h5>
                    <span class="badge bg-secondary">Guard: {{ $role->guard_name }}</span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('roles.update-permissions', $role->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <p class="text-muted mb-2">Check the permissions you want to assign to this role:</p>
                                <hr class="mt-0">
                            </div>
                            
                            @forelse($permissions as $permission)
                                <div class="col-md-3">
                                    <div class="p-3 border rounded shadow-sm bg-light d-flex align-items-center">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                   value="{{ $permission->id }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold ms-2 text-dark" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <div class="alert alert-warning d-inline-block">
                                        ⚠️ No permissions found for the guard: <strong>{{ $role->guard_name }}</strong>. Please create permissions for this guard first!
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 d-flex justify-content-between border-top pt-4">
                                <button type="submit" class="btn btn-success px-5 fw-bold" {{ $permissions->isEmpty() ? 'disabled' : '' }}>
                                    <i class="feather-save me-1"></i> Save Changes
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

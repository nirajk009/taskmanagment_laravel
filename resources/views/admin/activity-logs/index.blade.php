{{-- resources/views/admin/activity-logs/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Activity Logs</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">System Activity Logs</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" id="from_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" id="to_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>Action</label>
                                <select id="action" class="form-select">
                                    <option value="">All</option>
                                    <option value="created">Created</option>
                                    <option value="updated">Updated</option>
                                    <option value="deleted">Deleted</option>
                                    <option value="status_changed">Status Changed</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                    <option value="registered">Registered</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>User</label>
                                <select id="user_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach(App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary" id="filterBtn">Filter</button>
                                <button class="btn btn-secondary ms-2" id="clearBtn">Clear</button>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="activityLogsTable" class="display table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Model</th>
                                        <th>Description</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data loaded via Ajax -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let activityLogsTable;

    // Initialize DataTable
    activityLogsTable = $('#activityLogsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.activity-logs.data') }}",
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.action = $('#action').val();
                d.user_id = $('#user_id').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'user_name', name: 'user.name'},
            {data: 'action_badge', name: 'action'},
            {data: 'model_name', name: 'model_type'},
            {data: 'description', name: 'description'},
            {data: 'created_at_formatted', name: 'created_at'}
        ],
        order: [[5, 'desc']] // Order by created_at DESC
    });

    // Filter functionality
    $('#filterBtn').on('click', function() {
        activityLogsTable.ajax.reload();
    });

    $('#clearBtn').on('click', function() {
        $('#from_date, #to_date, #action, #user_id').val('');
        activityLogsTable.ajax.reload();
    });
});
</script>
@endsection

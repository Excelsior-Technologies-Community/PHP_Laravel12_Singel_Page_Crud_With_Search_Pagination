<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Activity Logs</h3>
        <div>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to Items</a>
            <form action="{{ route('activity-logs.clear-old') }}" method="POST" class="d-inline" onsubmit="return confirm('Clear logs older than 30 days?')">
                @csrf
                <button class="btn btn-warning">Clear Old Logs</button>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Model</th>
            <th>Description</th>
            <th>IP Address</th>
            <th>Time</th>
            <th width="100">Action</th>
        </tr>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->user->name ?? 'System' }}</td>
            <td><span class="badge bg-{{ $log->action == 'created' ? 'success' : ($log->action == 'deleted' || $log->action == 'force_deleted' ? 'danger' : 'warning') }}">{{ ucfirst($log->action) }}</span></td>
            <td>{{ $log->model_type ?? 'N/A' }} #{{ $log->model_id ?? 'N/A' }}</td>
            <td>{{ $log->description }}</td>
            <td>{{ $log->ip_address }}</td>
            <td>{{ $log->created_at->diffForHumans() }}</td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewLog({{ $log->id }})">View</button>
                <form action="{{ route('activity-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete log?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="mt-3">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- LOG DETAIL MODAL --}}
<div class="modal fade" id="logModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logModalBody">
                Loading...
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function viewLog(id) {
    fetch(`/activity-logs/${id}`)
        .then(r => r.json())
        .then(data => {
            const log = data.log;
            document.getElementById('logModalBody').innerHTML = `
                <p><strong>User:</strong> ${log.user?.name || 'System'}</p>
                <p><strong>Action:</strong> ${log.action}</p>
                <p><strong>Model:</strong> ${log.model_type || 'N/A'} #${log.model_id || 'N/A'}</p>
                <p><strong>Description:</strong> ${log.description}</p>
                <p><strong>IP:</strong> ${log.ip_address}</p>
                <p><strong>User Agent:</strong> ${log.user_agent || 'N/A'}</p>
                <p><strong>Time:</strong> ${log.created_at}</p>
                ${log.old_values ? `<h6>Old Values:</h6><pre>${JSON.stringify(log.old_values, null, 2)}</pre>` : ''}
                ${log.new_values ? `<h6>New Values:</h6><pre>${JSON.stringify(log.new_values, null, 2)}</pre>` : ''}
            `;
            new bootstrap.Modal(document.getElementById('logModal')).show();
        });
}
</script>
</body>
</html>

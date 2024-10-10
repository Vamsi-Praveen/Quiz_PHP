<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    // header("Location: login.php");
    echo "<script>window.location.href='login.php'</script>";
    exit();
}
?>
<?php
include('../config/db.php');

$query = "SELECT * FROM tests";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | View Tests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <?php include('./includes/sidebar.php')?>

        <!-- Main Content -->
        <div class="flex-1 bg-[#ecf0f5] p-4 lg:p-6">
            <h1 class="text-2xl font-semibold mb-4">View All Tests</h1>

            <div class="overflow-x-auto bg-white p-4 rounded-md shadow">
                <table id="testsTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b">Test ID</th>
                            <th class="px-4 py-2 border-b">Test Title</th>
                            <th class="px-4 py-2 border-b">Test Start Time</th>
                            <th class="px-4 py-2 border-b">Test End Time</th>
                            <th class="px-4 py-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['start_time']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['end_time']); ?></td>
                                    <td class="border-b px-4 py-2">
                                        <button class="bg-blue-500 text-white p-2 editBtn" data-id="<?php echo htmlspecialchars($row['id']); ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-start="<?php echo htmlspecialchars($row['start_time']); ?>" data-end="<?php echo htmlspecialchars($row['end_time']); ?>">Edit</button>
                                        <button class="bg-red-500 text-white deleteBtn p-2" data-id="<?php echo htmlspecialchars($row['id']); ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center">No tests available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-md shadow-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Edit Test</h2>
            <form id="editForm">
                <input type="hidden" id="editTestId" name="test_id">
                <div class="mb-4">
                    <label class="block text-gray-700">Test Title</label>
                    <input type="text" id="editTitle" name="title" class="border w-full p-2 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Start Time</label>
                    <input type="datetime-local" id="editStartTime" name="start_time" class="border w-full p-2 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">End Time</label>
                    <input type="datetime-local" id="editEndTime" name="end_time" class="border w-full p-2 rounded-lg" required>
                </div>
                <div class="text-right">
                    <button type="button" id="updateTest" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                    <button type="button" id="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#testsTable').DataTable();

    $('.editBtn').on('click', function () {
        const testId = $(this).data('id');
        const title = $(this).data('title');
        const start = $(this).data('start');
        const end = $(this).data('end');


        $('#editTestId').val(testId);
        $('#editTitle').val(title);
        $('#editStartTime').val(start);
        $('#editEndTime').val(end);


        $('#editModal').removeClass('hidden');
    });

    $('#closeModal').on('click', function () {
        $('#editModal').addClass('hidden');
    });


    $('#updateTest').on('click', function () {
        const testId = $('#editTestId').val();
        const title = $('#editTitle').val();
        const startTime = $('#editStartTime').val();
        const endTime = $('#editEndTime').val();


        $.ajax({
            type: 'POST',
            url: 'update_test.php',
            data: { id: testId, title: title, start_time: startTime, end_time: endTime },
            success: function (response) {
                alert('Test updated successfully!');
                location.reload(); 
            },
            error: function () {
                alert('Failed to update test.');
            }
        });
    });

    $('.deleteBtn').on('click', function () {
        const testId = $(this).data('id');
        if (confirm('Are you sure you want to delete this test?')) {
            $.ajax({
                type: 'POST',
                url: 'delete_test.php',
                data: { id: testId },
                success: function (response) {
                    alert('Test deleted successfully!');
                    location.reload(); 
                },
                error: function () {
                    alert('Failed to delete test.');
                }
            });
        }
    });
});
</script>

</html>

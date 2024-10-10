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

$query = "SELECT * FROM user";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | View Users</title>
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
            <h1 class="text-2xl font-semibold mb-4">View All Users</h1>

            <div class="overflow-x-auto bg-white p-4 rounded-md shadow">
                <table id="testsTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b">User ID</th>
                            <th class="px-4 py-2 border-b">Full Name</th>
                            <th class="px-4 py-2 border-b">Username</th>
                            <th class="px-4 py-2 border-b">Completed Tests</th>
                            <th class="px-4 py-2 border-b">Points</th>
                            <th class="px-4 py-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['ID']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['completed_tests']); ?></td>
                                    <td class="border-b px-4 py-2"><?php echo htmlspecialchars($row['points']); ?></td>
                                    <td class="border-b px-4 py-2">
                                        <button class="bg-blue-500 text-white p-2 editBtn" data-id="<?php echo htmlspecialchars($row['ID']); ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-username="<?php echo htmlspecialchars($row['username']); ?>" data-points="<?php echo htmlspecialchars($row['points']); ?>">Edit</button>
                                        <button class="bg-red-500 text-white deleteBtn p-2" data-id="<?php echo htmlspecialchars($row['ID']); ?>">Delete</button>
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
        <h2 class="text-lg font-semibold mb-4">Edit User</h2>
        <form id="editForm">
            <input type="hidden" id="editUserId" name="user_id">
            <div class="mb-4">
                <label class="block text-gray-700">Full Name</label>
                <input type="text" id="editName" name="name" class="border w-full p-2 rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" id="editUsername" name="username" class="border w-full p-2 rounded-lg" required disabled>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Points</label>
                <input type="number" id="editPoints" name="points" class="border w-full p-2 rounded-lg" required>
            </div>
            <div class="text-right">
                <button type="button" id="updateUser" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
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
        const userId = $(this).data('id');
        const name = $(this).data('name');
        const username = $(this).data('username');
        const points = $(this).data('points'); 

        $('#editUserId').val(userId);
        $('#editName').val(name);
        $('#editUsername').val(username);
        $('#editPoints').val(points);

        $('#editModal').removeClass('hidden');
    });

    $('#closeModal').on('click', function () {
        $('#editModal').addClass('hidden');
    });

    $('#updateUser').on('click', function () {
        const userId = $('#editUserId').val();
        const name = $('#editName').val();
        const username = $('#editUsername').val();
        const points = $('#editPoints').val();

        $.ajax({
            type: 'POST',
            url: 'update_user.php',
            data: { id: userId, name: name, username: username, points: points },
            success: function (response) {
                alert('User updated successfully!');
                location.reload(); 
            },
            error: function () {
                alert('Failed to update user.');
            }
        });
    });

    // Delete User Logic
    $('.deleteBtn').on('click', function () {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                type: 'POST',
                url: 'delete_user.php',
                data: { id: userId },
                success: function (response) {
                    alert('User deleted successfully!');
                    location.reload(); 
                },
                error: function () {
                    alert('Failed to delete user.');
                }
            });
        }
    });
});

</script>

</html>

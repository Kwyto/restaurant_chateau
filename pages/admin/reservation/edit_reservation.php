<?php
include '../includes/config.php';
header('Content-Type: application/json');

// Handle GET request - Tampilkan form edit
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid reservation ID is required']);
        exit;
    }

    $id = intval($_GET['id']);
    
    try {
        $query = "SELECT 
                    r.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone
                  FROM reservations r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservation = mysqli_fetch_assoc($result);

        if ($reservation) {
            echo json_encode([
                'success' => true,
                'data' => $reservation
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reservation not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Handle PUT request - Update data
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !is_numeric($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid reservation ID is required']);
        exit;
    }

    $id = intval($input['id']);
    
    // Validasi data yang diperlukan
    $requiredFields = ['reservation_date', 'reservation_time', 'table_number', 'guests', 'status'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field $field is required"]);
            exit;
        }
    }

    try {
        $query = "UPDATE reservations SET
                    reservation_date = ?,
                    reservation_time = ?,
                    table_number = ?,
                    guests = ?,
                    ocassion = ?,
                    special_requests = ?,
                    status = ?,
                    updated_at = NOW()
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssiisssi', 
            $input['reservation_date'],
            $input['reservation_time'],
            $input['table_number'],
            $input['guests'],
            $input['ocassion'] ?? null,
            $input['special_requests'] ?? null,
            $input['status'],
            $id
        );

        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Reservation updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update reservation');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Handle method tidak didukung
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

// Tutup koneksi
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Edit Reservation</h1>
        <div id="edit-form" class="bg-white p-6 rounded-lg shadow">
            <form id="reservation-edit-form">
                <input type="hidden" id="edit-id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kolom kiri -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="edit-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" id="edit-time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Table Number</label>
                            <input type="number" id="edit-table" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                    
                    <!-- Kolom kanan -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Number of Guests</label>
                            <input type="number" id="edit-guests" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Occasion</label>
                            <input type="text" id="edit-occasion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="edit-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Full width -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Special Requests</label>
                        <textarea id="edit-requests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-300 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ambil ID dari URL
        const urlParams = new URLSearchParams(window.location.search);
        const reservationId = urlParams.get('id');
        
        // Load data reservasi
        function loadReservationData() {
            fetch(`edit_reservation.php?id=${reservationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const res = data.data;
                        document.getElementById('edit-id').value = res.id;
                        document.getElementById('edit-date').value = res.reservation_date;
                        document.getElementById('edit-time').value = res.reservation_time;
                        document.getElementById('edit-table').value = res.table_number;
                        document.getElementById('edit-guests').value = res.guests;
                        document.getElementById('edit-occasion').value = res.ocassion;
                        document.getElementById('edit-status').value = res.status;
                        document.getElementById('edit-requests').value = res.special_requests || '';
                    } else {
                        alert('Error: ' + data.error);
                    }
                });
        }
        
        // Handle form submission
        document.getElementById('reservation-edit-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('edit-id').value,
                reservation_date: document.getElementById('edit-date').value,
                reservation_time: document.getElementById('edit-time').value,
                table_number: document.getElementById('edit-table').value,
                guests: document.getElementById('edit-guests').value,
                ocassion: document.getElementById('edit-occasion').value,
                status: document.getElementById('edit-status').value,
                special_requests: document.getElementById('edit-requests').value
            };
            
            updateReservation(formData);
        });
        
        // Update reservation
        function updateReservation(data) {
            fetch('edit_reservation.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reservation updated successfully!');
                    window.location.href = 'reservations.php';
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update reservation');
            });
        }
        
        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadReservationData);
    </script>
</body>
</html>
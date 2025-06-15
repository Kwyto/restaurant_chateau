<?php 
include '../includes/config.php';

// Redirect if not logged in or no reservation data
if(!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_data'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Process booking form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['booking_data'] = $_POST;
}

$reservation = $_SESSION['reservation_data'];
$guests = $reservation['guests'];

// Define table configurations untuk maksimal 8 orang
$tables = [
    // Baris 1 - Dekat Entrance
    1 => ['seats' => 2, 'position' => 'left', 'order' => 1],
    2 => ['seats' => 2, 'position' => 'center', 'order' => 1],
    3 => ['seats' => 4, 'position' => 'right', 'order' => 1],
    
    // Baris 2 - Tengah
    4 => ['seats' => 6, 'position' => 'left', 'order' => 2],
    5 => ['seats' => 8, 'position' => 'center', 'order' => 2],
    6 => ['seats' => 6, 'position' => 'right', 'order' => 2],
    
    // Baris 3 - Dekat Exit
    7 => ['seats' => 4, 'position' => 'left', 'order' => 3],
    8 => ['seats' => 4, 'position' => 'center', 'order' => 3],
    9 => ['seats' => 2, 'position' => 'right', 'order' => 3]
];

// Filter tables based on guest count
$availableTables = [];
foreach($tables as $tableId => $tableInfo) {
    if($tableInfo['seats'] >= $guests) {
        $availableTables[$tableId] = $tableInfo;
    }
}

// Function to render table HTML
function renderTable($tableId, $seats, $isAvailable) {
    $availableClass = $isAvailable ? 'table-option bg-gray-400 hover:bg-gold/80 transition-all cursor-pointer' : 'table-unavailable bg-gray-600 cursor-not-allowed opacity-50';
    $dataAttributes = $isAvailable ? "data-table-id='$tableId' data-seats='$seats'" : '';
    
    $tableHtml = "<div class='table-container relative flex justify-center'>";
    
    // Tentukan ukuran meja berdasarkan jumlah kursi
    switch($seats) {
        case 2:
            $tableHtml .= "<div class='$availableClass relative' style='width: 50px; height: 50px;' $dataAttributes>";
            $tableHtml .= "<span class='absolute inset-0 flex items-center justify-center text-white text-xs font-bold'>T$tableId<br/>($seats)</span>";
            if($isAvailable) {
                $tableHtml .= "
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 15px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 15px;'></div>";
            }
            break;
            
        case 4:
            // Existing 4-seater code
            $tableHtml .= "<div class='$availableClass relative' style='width: 60px; height: 80px;' $dataAttributes>";
            $tableHtml .= "<span class='absolute inset-0 flex items-center justify-center text-white text-xs font-bold'>T$tableId<br/>($seats)</span>";
            if($isAvailable) {
                $tableHtml .= "
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 20px; right: -8px; top: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 20px; left: -8px; top: 30px;'></div>";
            }
            break;
            
        case 6:
            $tableHtml .= "<div class='$availableClass relative' style='width: 70px; height: 120px;' $dataAttributes>";
            $tableHtml .= "<span class='absolute inset-0 flex items-center justify-center text-white text-xs font-bold'>T$tableId<br/>($seats)</span>";
            if($isAvailable) {
                $tableHtml .= "
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 10px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; right: 10px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 20px; right: -8px; top: 50px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 10px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; right: 10px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 20px; left: -8px; top: 50px;'></div>";
            }
            break;
            
        case 8:
            // Similar to existing 8-seater but with updated dimensions
            $tableHtml .= "<div class='$availableClass relative' style='width: 90px; height: 160px;' $dataAttributes>";
            $tableHtml .= "<span class='absolute inset-0 flex items-center justify-center text-white text-xs font-bold'>T$tableId<br/>($seats)</span>";
            if($isAvailable) {
                $tableHtml .= "
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 15px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; right: 15px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; right: -8px; top: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; right: -8px; bottom: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 15px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; right: 15px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; left: -8px; top: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; left: -8px; bottom: 30px;'></div>";
            }
            break;
            
        case 12:
            $tableHtml .= "<div class='$availableClass relative' style='width: 120px; height: 200px;' $dataAttributes>";
            $tableHtml .= "<span class='absolute inset-0 flex items-center justify-center text-white text-xs font-bold'>T$tableId<br/>($seats)</span>";
            if($isAvailable) {
                $tableHtml .= "
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; right: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; top: -8px; left: 50px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; right: -8px; top: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; right: -8px; top: 90px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; right: -8px; bottom: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; right: 20px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 20px; height: 12px; bottom: -8px; left: 50px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; left: -8px; top: 30px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; left: -8px; top: 90px;'></div>
                    <div class='chair absolute bg-gray-500' style='width: 12px; height: 25px; left: -8px; bottom: 30px;'></div>";
            }
            break;
    }
    
    $tableHtml .= "</div>";
    
    if(!$isAvailable) {
        $tableHtml .= "<div class='absolute inset-0 flex items-center justify-center'>";
        $tableHtml .= "<span class='text-red-400 text-xs font-bold bg-black/50 px-2 py-1 rounded'>Too Small</span>";
        $tableHtml .= "</div>";
    }
    
    $tableHtml .= "</div>";
    
    return $tableHtml;
}
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-serif font-bold mb-12 text-center" data-aos="fade-up">Choose Your Table</h1>
        
        <div class="mb-12 text-center" data-aos="fade-up" data-aos-delay="100">
            <p class="text-lg mb-4">Please select your preferred table for <?php echo $guests . ($guests == 1 ? ' person' : ' people'); ?></p>
            <div class="bg-gray-800 rounded-lg p-4 inline-block">
                <p class="text-gold font-semibold">Available Tables: <?php echo count($availableTables); ?></p>
                <p class="text-sm text-gray-300">
                    Showing tables with <?php echo $guests; ?>+ seats
                    <?php if(count($availableTables) < count($tables)): ?>
                        • <?php echo count($tables) - count($availableTables); ?> tables too small
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div class="relative" data-aos="zoom-in">
            <!-- Restaurant Layout Visualization -->
            <div class="restaurant-layout mx-auto mb-12 relative bg-black/95 rounded-lg p-8 border border-gold/30" style="width: 800px; height: 600px;">
                
                <!-- Legend (tanpa text) -->
                <div class="absolute top-4 left-4 bg-black/70 rounded-full px-2 py-1 shadow-lg backdrop-blur-sm border border-gold/50">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-gold/40 border border-gold/30"></div>
                        <div class="w-2 h-2 rounded-full bg-gold border border-gold/50"></div>
                        <div class="w-2 h-2 rounded-full bg-gray-600/50 border border-white/10"></div>
                    </div>
                </div>

                <!-- Entrance & Exit Labels -->
                <div class="absolute top-4 left-1/2 transform -translate-x-1/2">
                    <div class="bg-black/80 border border-gold/30 px-6 py-2 rounded">
                        <span class="text-gold font-semibold text-sm">ENTRANCE</span>
                    </div>
                </div>
                
                <div class="absolute bottom-4 right-4">
                    <div class="bg-black/80 border border-gold/30 px-4 py-2 rounded transform rotate-90">
                        <span class="text-gold font-semibold text-xs">EXIT</span>
                    </div>
                </div>
                
                <!-- Tables Grid Layout -->
                <div class="grid grid-cols-3 gap-12 h-full pt-16 pb-8 px-8">
                    <!-- Left Column -->
                    <div class="flex flex-col justify-between">
                        <?php echo renderTable(1, 2, isset($availableTables[1])); ?> <!-- 2 seats -->
                        <?php echo renderTable(4, 6, isset($availableTables[4])); ?> <!-- 6 seats -->
                        <?php echo renderTable(7, 4, isset($availableTables[7])); ?> <!-- 4 seats -->
                    </div>
                    
                    <!-- Center Column -->
                    <div class="flex flex-col justify-between items-center">
                        <?php echo renderTable(2, 2, isset($availableTables[2])); ?> <!-- 2 seats -->
                        <?php echo renderTable(5, 8, isset($availableTables[5])); ?> <!-- 8 seats -->
                        <?php echo renderTable(8, 4, isset($availableTables[8])); ?> <!-- 4 seats -->
                    </div>
                    
                    <!-- Right Column -->
                    <div class="flex flex-col justify-between">
                        <?php echo renderTable(3, 4, isset($availableTables[3])); ?> <!-- 4 seats -->
                        <?php echo renderTable(6, 6, isset($availableTables[6])); ?> <!-- 6 seats -->
                        <?php echo renderTable(9, 2, isset($availableTables[9])); ?> <!-- 2 seats -->
                    </div>
                </div>
            </div>
            
            <div class="text-center hidden" id="selected-table-info" data-aos="fade-up">
                <p class="text-lg mb-4">You've selected <span id="selected-table-number" class="text-gold font-bold">Table 1</span> 
                (<span id="selected-table-seats" class="text-gold">4 seats</span>)</p>
                <form id="seat-form" action="payment.php" method="POST">
                    <input type="hidden" id="selected-table" name="selected-table">
                    <button type="submit" id="proceed-to-payment" class="px-8 py-3 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<style>
.table-option {
    background-color: rgba(212, 175, 55, 0.15) !important;
    border: 1.5px solid rgba(212, 175, 55, 0.5);
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.1);
}

.table-option:hover {
    background-color: rgba(212, 175, 55, 0.25) !important;
    border-color: rgba(212, 175, 55, 0.8);
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);
}

.table-option.selected {
    background-color: rgba(212, 175, 55, 0.3) !important;
    border: 2px solid #d4af37;
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
}

.table-unavailable {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.chair {
    background-color: rgba(212, 175, 55, 0.3) !important;
}

.table-option:hover .chair {
    background-color: rgba(212, 175, 55, 0.6) !important;
}

.table-option.selected .chair {
    background-color: #d4af37 !important;
}

.table-unavailable .chair {
    background-color: rgba(255, 255, 255, 0.1) !important;
}

.text-too-small {
    color: #ff6b6b;
    background-color: rgba(0, 0, 0, 0.7);
    border: 1px solid rgba(255, 107, 107, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('.table-option');
    const selectedTableInfo = document.getElementById('selected-table-info');
    const selectedTableInput = document.getElementById('selected-table');
    const selectedTableNumber = document.getElementById('selected-table-number');
    const selectedTableSeats = document.getElementById('selected-table-seats');
    
    // Show available tables count
    console.log('Available tables for <?php echo $guests; ?> guests:', tables.length);
    
    if (tables.length > 0) {
        tables.forEach(table => {
            table.addEventListener('click', function() {
                // Remove selected class from all tables
                tables.forEach(t => t.classList.remove('selected'));
                
                // Add selected class to clicked table
                this.classList.add('selected');
                
                // Get table info
                const tableId = this.getAttribute('data-table-id');
                const tableSeats = this.getAttribute('data-seats');
                
                // Show selected table info
                selectedTableInfo.classList.remove('hidden');
                selectedTableNumber.textContent = 'Table ' + tableId;
                selectedTableSeats.textContent = tableSeats + ' seats';
                selectedTableInput.value = tableId;
                
                // Scroll to selection info
                selectedTableInfo.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            });
        });
    } else {
        // No available tables - show message
        const noTablesMessage = document.createElement('div');
        noTablesMessage.className = 'text-center text-red-400 mt-8';
        noTablesMessage.innerHTML = '<p class="text-lg">No tables available for <?php echo $guests; ?> guests.</p><p class="text-sm mt-2">Please contact restaurant for special arrangements.</p>';
        document.querySelector('.restaurant-layout').after(noTablesMessage);
    }
});
</script>
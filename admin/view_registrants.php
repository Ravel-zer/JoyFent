<?php
session_start();
require '../includes/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$event_id = $_GET['event_id'];
$event = $conn->query("SELECT * FROM events WHERE event_id = '$event_id'")->fetch_assoc();
$registrants = $conn->query("
    SELECT users.username, users.email, events.name AS event_name, registrations.registered_at 
    FROM registrations 
    JOIN users ON registrations.user_id = users.user_id 
    JOIN events ON registrations.event_id = events.event_id 
    WHERE registrations.event_id = '$event_id'
");

if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=registrants.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Username', 'Email', 'Event Name', 'Registered At']); // Header CSV

    while ($row = $registrants->fetch_assoc()) {
        fputcsv($output, [
            $row['username'], 
            $row['email'], 
            $row['event_name'], 
            $row['registered_at']
        ]);
    }
    fclose($output);
    exit();
}

if (isset($_POST['export_excel'])) {
    require '../vendor/autoload.php';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set Header
    $sheet->setCellValue('A1', 'Username');
    $sheet->setCellValue('B1', 'Email');
    $sheet->setCellValue('C1', 'Event Name');
    $sheet->setCellValue('D1', 'Registered At');

    // Isi Data
    $rowIndex = 2;
    $registrants_excel = $conn->query("
        SELECT users.username, users.email, events.name AS event_name, registrations.registered_at 
        FROM registrations 
        JOIN users ON registrations.user_id = users.user_id 
        JOIN events ON registrations.event_id = events.event_id 
        WHERE registrations.event_id = '$event_id'
    ");

    while ($row = $registrants_excel->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['username']);
        $sheet->setCellValue('B' . $rowIndex, $row['email']);
        $sheet->setCellValue('C' . $rowIndex, $row['event_name']);
        $sheet->setCellValue('D' . $rowIndex, $row['registered_at']);
        $rowIndex++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'registrants.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrants | <?php echo $event['name']; ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }

        .table-container {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #1f2937;
            color: white;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .action-buttons button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .action-buttons button:hover {
            opacity: 0.9;
        }

        .action-buttons i {
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<?php include 'navbar_admin.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Registrants for "<?php echo $event['name']; ?>"</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Event Name</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $registrants->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['event_name']; ?></td>
                        <td><?php echo $row['registered_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex space-x-4 action-buttons">
        <form method="POST">
            <button type="submit" name="export_csv" class="bg-blue-500 hover:bg-blue-700 text-white">
                <i data-feather="download"></i> Export to CSV
            </button>
        </form>
        <form method="POST">
            <button type="submit" name="export_excel" class="bg-green-500 hover:bg-green-700 text-white">
                <i data-feather="file"></i> Export to Excel
            </button>
        </form>
    </div>

    <div class="mt-8">
        <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
            Back to Dashboard
        </a>
    </div>
</div>

<script>
    feather.replace();  // Initialize Feather Icons
</script>
</body>
</html>
